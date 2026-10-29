<?php

declare(strict_types=1);

namespace Files\Test;

/**
 * A local HTTP/1.1 server the adaptive transfer tests run as a process of their own (php LoopbackServer.php), so that
 * the SDK's concurrent requests really overlap. It listens on two loopback ports, prints {"ports": [first, second]} on
 * one line, and serves until its standard input closes. Every connection gets one answer and is closed.
 *
 * It answers as the Files.com API (begin_upload, the end of an upload, a file's download URL), as storage (PUT
 * /storage/up for upload parts, GET /download for downloads, with Range and If-Match) and on its control paths:
 * POST /__control/reset (its body a JSON configuration, see defaults), GET /__control/journal (every request it
 * received, and the most storage and download requests it held open at once) and POST /__control/release (answers
 * every held request). A held request is answered once as many storage (or download) requests are open as the hold
 * asks for, or after its timeout. Some answers are deferred instead until what they wait for has happened (checked as
 * it serves): a range sent with a download's first, short-lived URL (expiring_url), whenever it comes, until that URL
 * has expired and a later one was given; and the first begin_upload for a path in api_stall, or the first end of an
 * upload to a path in end_stall, which takes effect and then stalls until stall_seconds have passed.
 */
final class LoopbackServer
{
    private $listeners = [];
    private $ports = [];
    private $clients = [];
    private $nextClient = 0;
    private $config;
    private $started;
    private $journal = [];
    private $open = 0;
    private $maxOpen = 0;
    private $held = [];
    private $holdDone = false;
    private $refs = 0;
    private $issued = [];
    private $attempts = [];
    private $uploads = [];
    private $downloadRequests = [];
    private $urls = [];
    private $arrivals = [];
    private $deferred = [];
    private $stalled = [];

    public static function main()
    {
        $server = new LoopbackServer();
        $server->listen();
        fwrite(STDOUT, json_encode(['ports' => $server->ports]) . "\n");
        fflush(STDOUT);
        $server->serve();
    }

    private function __construct()
    {
        $this->started = microtime(true);
        $this->config = self::defaults();
    }

    private static function defaults()
    {
        return [
            'upload' => [
                'partsize' => 4, 'parallel_parts' => true, 'retry_parts' => true, 'available_parts' => 10000, 'expired_first_grant' => false,
                'direct' => false, 'fail' => [], 'request_timeout' => [], 'request_timeout_padded' => [], 'no_etag' => [],
            ],
            // mode: ranged, ignore_range, ranges_once (the first range request honored, later ones answered whole),
            // no_etag or weak_etag.
            'download' => [
                'files' => [], 'mode' => 'ranged', 'change_after' => null, 'truncate' => [], 'wrong_range' => [], 'frontend' => false,
                'redirect_metadata' => false, 'expired_first_url' => false, 'omit_range_etag' => false, 'expiring_url' => null,
                'unsigned' => false, 'forbidden' => false, 'timeout_padded' => [],
            ],
            'hold' => null,
            'api_stall' => [],
            'end_stall' => [],
            'stall_seconds' => 30,
        ];
    }

    private function listen()
    {
        for ($index = 0; $index < 2; $index++) {
            $socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $error);
            if ($socket === false) {
                fwrite(STDERR, 'Cannot listen: ' . $error . "\n");
                exit(1);
            }

            stream_set_blocking($socket, false);
            $name = stream_socket_get_name($socket, false);
            $this->ports[] = (int) substr($name, strrpos($name, ':') + 1);
            $this->listeners[] = $socket;
        }
    }

    private function serve()
    {
        stream_set_blocking(STDIN, false);
        while (true) {
            $read = $this->listeners;
            $read[] = STDIN;
            $write = [];
            foreach ($this->clients as $client) {
                if ($client['out'] !== '') {
                    $write[] = $client['socket'];
                } elseif (!$client['answered']) {
                    $read[] = $client['socket'];
                }
            }

            $except = null;
            if (@stream_select($read, $write, $except, 0, 20000) === false) {
                usleep(1000);
                continue;
            }

            foreach ($read as $socket) {
                if ($socket === STDIN) {
                    $line = fread(STDIN, 1024);
                    if (($line === '' || $line === false) && feof(STDIN)) {
                        return;
                    }
                } elseif (in_array($socket, $this->listeners, true)) {
                    $this->accept($socket);
                } else {
                    $this->readFrom($this->clientOf($socket));
                }
            }

            foreach ($write as $socket) {
                $this->writeTo($this->clientOf($socket));
            }

            $this->checkHold();
            $this->checkDeferred();
        }
    }

    private function accept($listener)
    {
        $socket = @stream_socket_accept($listener, 0);
        if ($socket === false) {
            return;
        }

        stream_set_blocking($socket, false);
        $id = ++$this->nextClient;
        $port = $this->ports[array_search($listener, $this->listeners, true)];
        $this->clients[$id] = [
            'id' => $id, 'socket' => $socket, 'port' => $port, 'in' => '', 'head' => null, 'out' => '', 'answered' => false,
            'kind' => null, 'entry' => null, 'continued' => false,
        ];
    }

    private function clientOf($socket)
    {
        foreach ($this->clients as $id => $client) {
            if ($client['socket'] === $socket) {
                return $id;
            }
        }

        return null;
    }

    private function readFrom($id)
    {
        if ($id === null) {
            return;
        }

        $data = @fread($this->clients[$id]['socket'], 65536);
        if ($data === '' || $data === false) {
            if (feof($this->clients[$id]['socket'])) {
                $this->drop($id);
            }

            return;
        }

        $this->clients[$id]['in'] .= $data;
        $client = $this->clients[$id];
        if ($client['head'] === null) {
            $end = strpos($client['in'], "\r\n\r\n");
            if ($end === false) {
                return;
            }

            $lines = explode("\r\n", substr($client['in'], 0, $end));
            $parts = explode(' ', array_shift($lines));
            $headers = [];
            foreach ($lines as $line) {
                $colon = strpos($line, ':');
                if ($colon !== false) {
                    $headers[strtolower(trim(substr($line, 0, $colon)))] = trim(substr($line, $colon + 1));
                }
            }

            $target = isset($parts[1]) ? $parts[1] : '/';
            $question = strpos($target, '?');
            $this->clients[$id]['head'] = [
                'method' => $parts[0], 'path' => $question === false ? $target : substr($target, 0, $question),
                'query' => $question === false ? '' : substr($target, $question + 1), 'headers' => $headers,
                'length' => isset($headers['content-length']) ? (int) $headers['content-length'] : 0,
            ];
            $this->clients[$id]['in'] = (string) substr($client['in'], $end + 4);
        }

        $head = $this->clients[$id]['head'];
        if (strlen($this->clients[$id]['in']) < $head['length']) {
            $expect = isset($head['headers']['expect']) ? strtolower($head['headers']['expect']) : '';
            if ($expect === '100-continue' && !$this->clients[$id]['continued']) {
                $this->clients[$id]['continued'] = true;
                $this->clients[$id]['out'] .= "HTTP/1.1 100 Continue\r\n\r\n";
            }

            return;
        }

        $body = (string) substr($this->clients[$id]['in'], 0, $head['length']);
        $this->clients[$id]['answered'] = true;
        try {
            $this->handle($id, $head, $body);
        } catch (\Exception $e) {
            $this->send($id, [500, ['Content-Type' => 'text/plain'], get_class($e) . ': ' . $e->getMessage(), null]);
        }
    }

    private function writeTo($id)
    {
        if ($id === null) {
            return;
        }

        $written = @fwrite($this->clients[$id]['socket'], $this->clients[$id]['out']);
        if ($written === false) {
            $this->drop($id);
            return;
        }

        $this->clients[$id]['out'] = (string) substr($this->clients[$id]['out'], $written);
        if ($this->clients[$id]['out'] === '' && $this->clients[$id]['answered'] && $this->clients[$id]['entry'] === 'sent') {
            fclose($this->clients[$id]['socket']);
            unset($this->clients[$id]);
        }
    }

    // A connection that closed before its answer was sent: its request was given up.
    private function drop($id)
    {
        unset($this->deferred[$id]);
        if (isset($this->held[$id])) {
            unset($this->held[$id]);
            $this->open--;
            $entry = $this->clients[$id]['entry'];
            if (is_int($entry)) {
                $this->journal[$entry]['abandoned'] = true;
            }
        }

        @fclose($this->clients[$id]['socket']);
        unset($this->clients[$id]);
    }

    private function handle($id, array $head, $body)
    {
        $port = $this->clients[$id]['port'];
        $path = $head['path'];
        parse_str($head['query'], $query);
        if (strpos($path, '/__control/') === 0) {
            $this->control($id, $head, $body);
            return;
        }

        $json = $body === '' ? null : json_decode($body, true);
        $entry = count($this->journal);
        $this->journal[] = [
            'seq' => $entry + 1, 'port' => $port, 'method' => $head['method'], 'path' => $path, 'query' => $head['query'],
            'headers' => $head['headers'], 'body_bytes' => strlen($body), 'body_sha256' => hash('sha256', $body), 'json' => $json,
            'received_ms' => $this->elapsed(), 'answered_ms' => null, 'status' => null, 'open_at_arrival' => null, 'kind' => 'api',
        ];
        $this->clients[$id]['entry'] = $entry;
        if ($head['method'] === 'POST' && strpos($path, '/api/rest/v1/file_actions/begin_upload/') === 0) {
            $target = rawurldecode(substr($path, strlen('/api/rest/v1/file_actions/begin_upload/')));
            $answer = $this->beginUpload($target, (array) $json);
            if (in_array($target, $this->config['api_stall'], true)) {
                $this->stall($id, $entry, 'begin:' . $target, $answer);
            } else {
                $this->send($id, $answer);
            }
        } elseif ($head['method'] === 'POST' && strpos($path, '/api/rest/v1/files/') === 0) {
            $destination = rawurldecode(substr($path, strlen('/api/rest/v1/files/')));
            $ref = isset($json['ref']) ? $json['ref'] : null;
            if ($ref !== null && isset($this->uploads[$ref])) {
                $this->uploads[$ref]['ended'] = $json;
            }

            $answer = [201, ['Content-Type' => 'application/json'], json_encode(['path' => $destination]), null];
            if (in_array($destination, $this->config['end_stall'], true)) {
                $this->stall($id, $entry, 'end:' . $destination, $answer);
            } else {
                $this->send($id, $answer);
            }
        } elseif ($head['method'] === 'GET' && strpos($path, '/api/rest/v1/files/') === 0) {
            $this->send($id, $this->metadata($port, $path, $head['query']));
        } elseif ($head['method'] === 'PUT' && $path === '/storage/up') {
            $this->arrive($id, 'storage', $this->storeUpload($query, $body));
        } elseif ($head['method'] === 'GET' && $path === '/download') {
            $this->arrive($id, 'download', $this->download($query, $head['headers']));
        } else {
            $this->send($id, [404, ['Content-Type' => 'application/json'], '{"type":"not-found","http-code":404,"error":"Not Found"}', null]);
        }
    }

    private function control($id, array $head, $body)
    {
        $this->clients[$id]['entry'] = 'control';
        if ($head['path'] === '/__control/reset') {
            $given = json_decode($body, true);
            $this->config = array_replace_recursive(self::defaults(), is_array($given) ? $given : []);
            $this->journal = [];
            $this->maxOpen = $this->open;
            $this->held = [];
            $this->holdDone = false;
            $this->issued = $this->attempts = $this->uploads = $this->downloadRequests = $this->urls = $this->arrivals = $this->deferred = [];
            $this->stalled = [];
        } elseif ($head['path'] === '/__control/release') {
            $this->releaseHeld();
        } elseif ($head['path'] !== '/__control/journal') {
            $this->send($id, [404, [], '', null]);
            return;
        }

        $answer = ['entries' => $this->journal, 'max_open' => $this->maxOpen, 'uploads' => $this->uploads];
        $this->send($id, [200, ['Content-Type' => 'application/json'], json_encode($answer), null]);
    }

    private function beginUpload($path, array $params)
    {
        $settings = $this->config['upload'];
        $ref = isset($params['ref']) ? $params['ref'] : null;
        if ($ref === null) {
            $ref = 'ref-' . (++$this->refs);
            $this->uploads[$ref] = ['path' => $path, 'first' => $params, 'parts' => [], 'ended' => null];
        } elseif (!isset($this->uploads[$ref])) {
            return [404, ['Content-Type' => 'application/json'], '{"type":"not-found","http-code":404,"error":"No such upload"}', null];
        }

        $first = isset($params['part']) ? (int) $params['part'] : 1;
        $count = isset($params['parts']) ? (int) $params['parts'] : 1;
        $grants = [];
        for ($number = $first; $number < $first + $count; $number++) {
            $key = $ref . ':' . $number;
            $this->issued[$key] = (isset($this->issued[$key]) ? $this->issued[$key] : 0) + 1;
            $expired = $settings['expired_first_grant'] && $this->issued[$key] === 1;
            $direct = null;
            if ($settings['direct']) {
                $direct = ['version' => 1, 'server_name' => 'agent-1', 'addresses' => ['127.0.0.1'], 'direct_uri' => 'https://agent.example.test', 'ca_pem' => "-----BEGIN CERTIFICATE-----\nMIIB\n-----END CERTIFICATE-----"];
            }

            $grants[] = [
                'part_number' => $number, 'partsize' => $settings['partsize'], 'ref' => $ref, 'http_method' => 'PUT',
                'upload_uri' => 'http://127.0.0.1:' . $this->ports[0] . '/storage/up?ref=' . rawurlencode($ref) . '&part=' . $number . '&gen=' . $this->issued[$key] . '&' . self::signature($expired),
                'headers' => new \stdClass(), 'parameters' => null, 'send' => null, 'parallel_parts' => $settings['parallel_parts'],
                'retry_parts' => $settings['retry_parts'], 'available_parts' => $settings['available_parts'], 'action' => 'put/write',
                'direct_connection_info' => $direct, 'path' => $path,
            ];
        }

        return [200, ['Content-Type' => 'application/json'], json_encode($grants), null];
    }

    private function storeUpload(array $query, $body)
    {
        $settings = $this->config['upload'];
        $ref = isset($query['ref']) ? $query['ref'] : '';
        $part = isset($query['part']) ? (string) $query['part'] : '';
        $key = $ref . ':' . $part;
        $attempt = $this->attempts[$key] = (isset($this->attempts[$key]) ? $this->attempts[$key] : 0) + 1;
        if (self::expired($query)) {
            return [403, ['Content-Type' => 'application/xml'], '<Error><Code>AccessDenied</Code><Message>Request has expired</Message></Error>', null];
        }

        if (isset($settings['request_timeout'][$part]) && $attempt <= $settings['request_timeout'][$part]) {
            return [400, ['Content-Type' => 'application/xml'], self::timeoutXml(), null];
        }

        if (isset($settings['request_timeout_padded'][$part]) && $attempt <= $settings['request_timeout_padded'][$part]) {
            return [400, ['Content-Type' => 'application/xml'], self::paddedTimeout(), null];
        }

        if (isset($settings['fail'][$part][$attempt - 1])) {
            $status = (int) $settings['fail'][$part][$attempt - 1];
            $headers = $status === 503 || $status === 429 ? ['Retry-After' => '0'] : [];
            return [$status, $headers, '', null];
        }

        $sha = hash('sha256', $body);
        if (isset($this->uploads[$ref])) {
            $this->uploads[$ref]['parts'][$part][] = ['bytes' => strlen($body), 'sha256' => $sha];
        }

        if (in_array((int) $part, array_map('intval', $settings['no_etag']), true)) {
            return [200, [], '', null];
        }

        return [200, ['ETag' => '"etag-' . $part . '-' . substr($sha, 0, 8) . '"'], '', null];
    }

    private function metadata($port, $path, $query)
    {
        $settings = $this->config['download'];
        if ($settings['redirect_metadata'] && $port === $this->ports[0]) {
            $location = 'http://127.0.0.1:' . $this->ports[1] . $path . ($query === '' ? '' : '?' . $query);
            return [302, ['Location' => $location], '', null];
        }

        $name = rawurldecode(substr($path, strlen('/api/rest/v1/files/')));
        if (!isset($settings['files'][$name])) {
            return [404, ['Content-Type' => 'application/json'], '{"type":"not-found","http-code":404,"error":"Not Found"}', null];
        }

        // Each URL names its generation (1 for the first given for the file); the first lives expiring_url seconds when
        // that is set, and an unsigned URL states no expiry at all.
        $this->urls[$name] = (isset($this->urls[$name]) ? $this->urls[$name] : 0) + 1;
        $generation = $this->urls[$name];
        $expired = $settings['expired_first_url'] && $generation === 1;
        $lifetime = $generation === 1 && $settings['expiring_url'] !== null ? (int) $settings['expiring_url'] : 3600;
        $signature = $settings['unsigned'] ? '' : '&' . self::signature($expired, $lifetime);
        $uri = 'http://127.0.0.1:' . $this->ports[0] . '/download?name=' . rawurlencode($name) . '&gen=' . $generation . $signature;
        return [200, ['Content-Type' => 'application/json'], json_encode(['path' => $name, 'download_uri' => $uri]), null];
    }

    private function download(array $query, array $headers)
    {
        $settings = $this->config['download'];
        $name = isset($query['name']) ? $query['name'] : '';
        if (!isset($settings['files'][$name])) {
            return [404, [], '', null];
        }

        $range = isset($headers['range']) ? $headers['range'] : null;
        $expiry = self::expiry($query);
        if ($settings['expiring_url'] !== null && isset($query['gen']) && $query['gen'] === '1' && $range !== null && strpos($range, 'bytes=0-') !== 0 && $expiry !== null) {
            // A later range sent with the first, short-lived URL, whenever it comes: refused as expired once that URL
            // has expired and a later URL has been given for the file (or 10 s after it expired, whichever came first).
            return [403, ['Content-Type' => 'application/xml'], '<Error><Code>AccessDenied</Code><Message>Request has expired</Message></Error>', null, function () use ($expiry, $name) {
                return time() > $expiry && (time() > $expiry + 10 || (isset($this->urls[$name]) && $this->urls[$name] >= 2));
            }];
        }

        if (self::expired($query)) {
            return [403, ['Content-Type' => 'application/xml'], '<Error><Code>AccessDenied</Code><Message>Request has expired</Message></Error>', null];
        }

        if ($settings['frontend']) {
            return [200, ['Content-Type' => 'text/html', 'X-Files-Frontend-App' => 'true'], '<html><body>Files.com</body></html>', null];
        }

        if ($settings['forbidden']) {
            return [403, ['Content-Type' => 'application/xml'], '<Error><Code>AccessDenied</Code><Message>Access Denied</Message></Error>', null];
        }

        $count = $this->downloadRequests[$name] = (isset($this->downloadRequests[$name]) ? $this->downloadRequests[$name] : 0) + 1;
        $content = base64_decode($settings['files'][$name]['content']);
        $etag = $settings['files'][$name]['etag'];
        if ($settings['change_after'] !== null && $count > $settings['change_after']) {
            $etag = '"changed"';
            $content = strrev($content);
        }

        $total = strlen($content);
        $sent = $settings['mode'] === 'no_etag' ? null : ($settings['mode'] === 'weak_etag' ? 'W/' . $etag : $etag);
        if (isset($headers['if-match']) && $headers['if-match'] !== $etag) {
            return [412, [], '', null];
        }

        $whole = $settings['mode'] === 'ignore_range' || ($settings['mode'] === 'ranges_once' && $count > 1);
        if ($range === null || $whole || !preg_match('/\Abytes=([0-9]+)-([0-9]*)\z/', $range, $match)) {
            $answer = ['Content-Type' => 'application/octet-stream'];
            if ($sent !== null) {
                $answer['ETag'] = $sent;
            }

            return [200, $answer, $content, isset($settings['truncate']['whole']) && $count <= $settings['truncate']['whole'] ? (int) floor($total / 2) : null];
        }

        if ($total === 0) {
            return [416, ['Content-Range' => 'bytes */0'], '', null];
        }

        $start = (int) $match[1];
        $end = $match[2] === '' ? $total - 1 : min((int) $match[2], $total - 1);
        $key = $name . ':' . $start;
        $this->attempts[$key] = (isset($this->attempts[$key]) ? $this->attempts[$key] : 0) + 1;
        if (isset($settings['timeout_padded'][(string) $start]) && $this->attempts[$key] <= $settings['timeout_padded'][(string) $start]) {
            return [400, ['Content-Type' => 'application/xml'], self::paddedTimeout(), null];
        }

        $answer = ['Content-Type' => 'application/octet-stream', 'Content-Range' => 'bytes ' . $start . '-' . $end . '/' . $total];
        if (isset($settings['wrong_range'][(string) $start]) && $this->attempts[$key] <= $settings['wrong_range'][(string) $start]) {
            $answer['Content-Range'] = 'bytes ' . ($start + 1) . '-' . $end . '/' . $total;
        }

        if ($sent !== null && !($settings['omit_range_etag'] && isset($headers['if-match']))) {
            $answer['ETag'] = $sent;
        }

        $truncate = null;
        if (isset($settings['truncate'][(string) $start]) && $this->attempts[$key] <= $settings['truncate'][(string) $start]) {
            $truncate = (int) floor(($end - $start + 1) / 2);
        }

        return [206, $answer, (string) substr($content, $start, $end - $start + 1), $truncate];
    }

    // A storage or download request: answered now, or held as the hold asks.
    private function arrive($id, $kind, array $response)
    {
        $this->open++;
        $this->maxOpen = max($this->maxOpen, $this->open);
        $entry = $this->clients[$id]['entry'];
        $this->journal[$entry]['kind'] = $kind;
        $this->journal[$entry]['open_at_arrival'] = $this->open;
        $this->clients[$id]['kind'] = $kind;
        if (isset($response[4])) {
            // Answered once what it waits for has happened (checkDeferred).
            $this->journal[$entry]['deferred'] = true;
            $this->deferred[$id] = ['response' => $response, 'release' => $response[4]];
            return;
        }

        $hold = $this->config['hold'];
        $this->arrivals[$kind] = (isset($this->arrivals[$kind]) ? $this->arrivals[$kind] : 0) + 1;
        $after = $hold !== null && isset($hold['after']) ? (int) $hold['after'] : 0;
        if ($hold !== null && !$this->holdDone && $hold['kind'] === $kind && $this->arrivals[$kind] > $after) {
            $this->held[$id] = ['response' => $response, 'since' => microtime(true)];
            $this->checkHold();
            return;
        }

        $this->send($id, $response);
    }

    private function checkHold()
    {
        $hold = $this->config['hold'];
        if ($hold === null || !$this->held) {
            return;
        }

        if ($this->open >= $hold['until_open']) {
            $this->holdDone = true;
            $this->releaseHeld();
            return;
        }

        foreach ($this->held as $id => $waiting) {
            if (microtime(true) - $waiting['since'] >= $hold['timeout']) {
                unset($this->held[$id]);
                $this->journal[$this->clients[$id]['entry']]['hold_timed_out'] = true;
                $this->send($id, $waiting['response']);
            }
        }
    }

    private function releaseHeld()
    {
        $held = $this->held;
        $this->held = [];
        foreach ($held as $id => $waiting) {
            $this->send($id, $waiting['response']);
        }
    }

    // Answers each deferred request whose wait is over.
    private function checkDeferred()
    {
        foreach ($this->deferred as $id => $waiting) {
            if (call_user_func($waiting['release'])) {
                unset($this->deferred[$id]);
                $this->send($id, $waiting['response']);
            }
        }
    }

    // Answers API request $id, whose journal entry is $entry and which has already taken effect, with $response: now,
    // unless it is the first for $key, which stalls (its entry says so) and is answered only once stall_seconds have
    // passed (checkDeferred).
    private function stall($id, $entry, $key, array $response)
    {
        if (isset($this->stalled[$key])) {
            $this->send($id, $response);
            return;
        }

        $this->stalled[$key] = true;
        $since = microtime(true);
        $limit = (float) $this->config['stall_seconds'];
        $this->journal[$entry]['stalled'] = true;
        $this->deferred[$id] = ['response' => $response, 'release' => function () use ($since, $limit) {
            return microtime(true) - $since >= $limit;
        }];
    }

    // Queues an answer: [status, headers, body, and how many of the body's bytes to send before closing, or null].
    private function send($id, array $response)
    {
        if (!isset($this->clients[$id])) {
            return;
        }

        list($status, $headers, $body, $truncate) = $response;
        $head = 'HTTP/1.1 ' . $status . ' ' . self::reason($status) . "\r\n";
        $headers['Content-Length'] = (string) strlen($body);
        $headers['Connection'] = 'close';
        foreach ($headers as $name => $value) {
            $head .= $name . ': ' . $value . "\r\n";
        }

        $this->clients[$id]['out'] .= $head . "\r\n" . ($truncate === null ? $body : (string) substr($body, 0, $truncate));
        $entry = $this->clients[$id]['entry'];
        if (is_int($entry)) {
            $this->journal[$entry]['status'] = $status;
            $this->journal[$entry]['answered_ms'] = $this->elapsed();
            if ($truncate !== null) {
                $this->journal[$entry]['truncated'] = true;
            }
        }

        if ($this->clients[$id]['kind'] !== null) {
            $this->open--;
        }

        $this->clients[$id]['entry'] = 'sent';
    }

    private function elapsed()
    {
        return round((microtime(true) - $this->started) * 1000, 3);
    }

    // The SigV4-style expiry of a URL this server signs: current for $lifetime seconds (an hour unless said), or expired
    // since 2000.
    private static function signature($expired, $lifetime = 3600)
    {
        $date = $expired ? '20000101T000000Z' : gmdate('Ymd\THis\Z');
        return 'X-Amz-Date=' . $date . '&X-Amz-Expires=' . ($expired ? '60' : (string) $lifetime);
    }

    private static function expired(array $query)
    {
        $expiry = self::expiry($query);
        return $expiry !== null && $expiry < time();
    }

    // When a URL this server signed stops working, as a Unix time, or null for an unsigned one.
    private static function expiry(array $query)
    {
        if (!isset($query['X-Amz-Date'], $query['X-Amz-Expires'])) {
            return null;
        }

        $time = gmmktime(
            (int) substr($query['X-Amz-Date'], 9, 2),
            (int) substr($query['X-Amz-Date'], 11, 2),
            (int) substr($query['X-Amz-Date'], 13, 2),
            (int) substr($query['X-Amz-Date'], 4, 2),
            (int) substr($query['X-Amz-Date'], 6, 2),
            (int) substr($query['X-Amz-Date'], 0, 4)
        );
        return $time + (int) $query['X-Amz-Expires'];
    }

    // S3's RequestTimeout error.
    private static function timeoutXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<Error><Code>RequestTimeout</Code><Message>Your socket connection to the server was not read from or written to within the timeout period.</Message></Error>';
    }

    // S3's RequestTimeout error padded with spaces to 65,536 bytes, the most a client keeps of an error, and followed by
    // more: whole, it is not that error, though its first 65,536 bytes look like it.
    private static function paddedTimeout()
    {
        return str_pad(self::timeoutXml(), 65536, ' ') . '<More/>';
    }

    private static function reason($status)
    {
        $reasons = [
            100 => 'Continue', 200 => 'OK', 201 => 'Created', 206 => 'Partial Content', 302 => 'Found', 400 => 'Bad Request', 403 => 'Forbidden',
            404 => 'Not Found', 412 => 'Precondition Failed', 416 => 'Range Not Satisfiable', 429 => 'Too Many Requests',
            500 => 'Internal Server Error', 503 => 'Service Unavailable',
        ];
        return isset($reasons[$status]) ? $reasons[$status] : 'Status';
    }
}

LoopbackServer::main();
