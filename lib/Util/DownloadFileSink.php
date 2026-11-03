<?php

namespace Files\Util;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

/**
 * Guzzle sink for a request that downloads to a file path, with the SDK owning the file handle, so that the file is
 * closed as soon as the request returns or throws, even while the caller keeps the exception.
 *
 * Api::sendVerbatim() is the owner: it chooses this sink for a path it can own (see canOwn()), starts a new file for
 * each transfer of the request (see newFilePerTransfer()) and calls release() once the request has returned or failed,
 * after any error body or response data has been read from the file. The file is opened the way Guzzle opens a path
 * sink, with mode w+, creating or truncating it, and it is:
 *
 * - unopened until a transfer first uses the sink, so a request that fails before any response leaves the destination
 *   untouched;
 * - closed before the next transfer of the same request, which opens its own new file;
 * - released without ever being opened for that, after which the sink cannot be used.
 *
 * Only a path becomes one of these. A caller's own resource or stream sink stays the caller's: the SDK neither replaces
 * nor closes it.
 *
 * @internal
 */
class DownloadFileSink implements StreamInterface
{
    use StreamDecoratorTrait;

    /** @var StreamInterface The current transfer's file, opened on first use by StreamDecoratorTrait::__get() */
    private $stream;
    private $path;
    private $released = false;

    /**
     * Whether a request with $destination as its sink can use this sink, rather than Guzzle opening the path.
     *
     * - $destination must be a path in an existing directory. Guzzle reports a missing directory itself, before it
     *   sends the request.
     * - The request must be sent by Guzzle's cURL handler, the SDK's default. Each call to it delivers at most one
     *   response to the sink, and the SDK starts a new file before each call (see newFilePerTransfer()). A handler set
     *   through Files::setHandler() may be built from middleware that sends a request again within one call, where the
     *   SDK cannot start a new file, or may answer with a response whose body it keeps. Such handlers keep Guzzle's
     *   path handling, and the SDK closes nothing of theirs.
     */
    public static function canOwn($destination, $handler)
    {
        return is_string($destination) && is_dir(dirname($destination)) && $handler instanceof CurlHandler;
    }

    /**
     * Wraps a transport handler so that a DownloadFileSink starts a new file before each call.
     *
     * Api installs this beneath all of its middleware, where every call is one transfer: the first attempt, a retry, a
     * followed redirect or a region relocation. The new file cannot wait for the response: for an encoded response,
     * Guzzle reads the sink's size while creating it, before any response callback runs. Closing the previous transfer's
     * file never opens the next.
     */
    public static function newFilePerTransfer(callable $handler)
    {
        return function ($request, array $options) use ($handler) {
            if (isset($options['sink']) && $options['sink'] instanceof self) {
                $options['sink']->closeFile();
            }

            return $handler($request, $options);
        };
    }

    public function __construct($path)
    {
        $this->path = $path;
        unset($this->stream);
    }

    /**
     * Closes the file if a transfer opened it, never opening it to do so. The sink cannot be used afterwards.
     *
     * A failure to close is thrown, unless $requestFailed: the request's own failure is then the one to report, and a
     * failure to close cannot replace it.
     */
    public function release($requestFailed = false)
    {
        $this->released = true;
        if (!$requestFailed) {
            $this->closeFile();
            return;
        }

        try {
            $this->closeFile();
        } catch (\Exception $closeFailure) {
            // The request's own failure is reported instead.
        } catch (\Throwable $closeFailure) {
            // As above, for an error from PHP 7 or later.
        }
    }

    protected function createStream()
    {
        if ($this->released) {
            throw new \RuntimeException('The download file sink has been released');
        }

        // Guzzle's own path sink is a LazyOpenStream, so open failures read the same. Opening it before it is kept
        // leaves nothing to close if opening fails.
        $file = new LazyOpenStream($this->path, 'w+');
        $file->tell();

        return $file;
    }

    // Closes the current transfer's file, if one was opened. isset() never opens it, since StreamDecoratorTrait defines
    // no __isset(). The file is forgotten before it is closed, so a failed close leaves nothing to close again.
    private function closeFile()
    {
        if (isset($this->stream)) {
            $file = $this->stream;
            unset($this->stream);
            $file->close();
        }
    }
}
