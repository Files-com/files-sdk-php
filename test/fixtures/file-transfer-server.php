<?php

// Loopback Files.com API and download endpoints for FileTransferTest, run by `php -S`. The remote path of each SDK
// call names the scenario to serve; a scenario ending in "-once" is served for the first metadata request, and every
// later one downloads the full file. Each request's method, path and Range header are recorded in requests.jsonl.

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$payload = str_repeat("known transfer bytes\n", 1024);
$stateDir = getenv('FILES_TEST_STATE_DIR');

file_put_contents("$stateDir/requests.jsonl", json_encode([
    'line' => $_SERVER['REQUEST_METHOD'] . ' ' . $path,
    'range' => isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : null,
]) . "\n", FILE_APPEND);

$respondWithJson = function ($status, $data) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
};

$serveDownload = function ($scenario) use ($payload) {
    if (preg_match('/^redirect-to-(.+)$/', $scenario, $match)) {
        // Guzzle writes this body to the sink too, before it follows the redirect.
        header("Location: /download/{$match[1]}", true, 302);
        echo 'redirect body';
    } elseif ($scenario === 'not-found') {
        // A Files.com error body, which Guzzle writes to the sink like any other.
        http_response_code(404);
        header('Content-Type: application/json');
        echo '{"type":"not-found","http-code":404,"error":"Not Found.  This may be related to your permissions."}';
    } else {
        $start = 0;
        $end = strlen($payload) - 1;
        if (isset($_SERVER['HTTP_RANGE']) && preg_match('/^bytes=(\d*)-(\d*)$/', $_SERVER['HTTP_RANGE'], $range)) {
            http_response_code(206);
            $start = intval($range[1]);
            $end = $range[2] === '' ? $end : intval($range[2]);
        }
        $body = $scenario === 'empty' ? '' : substr($payload, $start, $end - $start + 1);

        // File::downloadToStream reads Content-Length and Content-Range from every download response. A truncated
        // response ends after 4,096 bytes of the length it announces.
        header('Content-Length: ' . strlen($body));
        header($body === '' ? 'Content-Range: bytes */0' : "Content-Range: bytes $start-$end/" . strlen($payload));
        echo $scenario === 'truncated' ? substr($body, 0, 4096) : $body;
    }
};

if (preg_match('@^/api/rest/v1/files/(.+)$@', $path, $match)) {
    // A file's metadata, naming the download to serve.
    $scenario = rawurldecode($match[1]);
    if ($scenario === 'metadata-error') {
        $respondWithJson(503, ['type' => 'service-unavailable', 'error' => 'Fixture failure']);
    } else {
        $port = $scenario === 'connection-refused' ? getenv('FILES_TEST_CLOSED_PORT') : $_SERVER['SERVER_PORT'];
        $download = $scenario;
        if (substr($scenario, -5) === '-once') {
            $servedOnce = "$stateDir/served-$scenario";
            $download = file_exists($servedOnce) ? 'full' : substr($scenario, 0, -5);
            touch($servedOnce);
        }
        $respondWithJson(200, ['path' => $scenario, 'download_uri' => "http://127.0.0.1:$port/download/$download"]);
    }
} elseif ($path === '/api/rest/v1/region') {
    // An API request answered from the wrong region, naming the site's host.
    $respondWithJson(403, [
        'type' => 'not-authenticated/lockout-region-mismatch',
        'http-code' => 403,
        'title' => 'Lockout Region Mismatch',
        'error' => 'Lockout region mismatch.',
        'data' => ['host' => 'other.example.test'],
    ]);
} elseif (preg_match('@^/download/(.+)$@', $path, $match)) {
    $serveDownload($match[1]);
} else {
    $respondWithJson(404, ['type' => 'not-found', 'error' => 'Unknown fixture route']);
}
