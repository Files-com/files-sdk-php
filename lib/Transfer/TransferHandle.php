<?php

declare(strict_types=1);

namespace Files\Transfer;

/**
 * One upload or download a TransferJob runs, as TransferJob's uploadFile, uploadData, downloadToFile and
 * downloadToStream return it. Its status is queued (not yet started), active, paused (TransferJob::pause stopped it with
 * its completed work kept; TransferJob::resume continues it in the same job), completed, failed (error() says why) or
 * cancelled (the job was cancelled, or stopped by another transfer's failure or a callback's exception, before it
 * ended).
 */
final class TransferHandle
{
    private $transfer;

    // Made by TransferJob.
    public function __construct($transfer)
    {
        $this->transfer = $transfer;
    }

    // upload or download.
    public function direction()
    {
        return $this->transfer->direction();
    }

    // The Files.com path uploaded to or downloaded from.
    public function path()
    {
        return $this->transfer->remotePath();
    }

    public function status()
    {
        return $this->transfer->state();
    }

    // What stopped a failed transfer; null otherwise.
    public function error()
    {
        return $this->transfer->error();
    }

    // The bytes the transfer moves: null for a stream uploaded without its size, and for a download before its size is
    // known.
    public function totalBytes()
    {
        return $this->transfer->totalBytes();
    }

    // The unique bytes accepted so far: parts acknowledged by storage, or ranges received and verified. Bytes sent again
    // by a retry count once.
    public function transferredBytes()
    {
        return $this->transfer->completedBytes();
    }

    // A completed download's received and total bytes, as File::downloadToFile reports them; null otherwise.
    public function result()
    {
        return $this->transfer->result();
    }

    // What the transfer holds now, for inspection: its status, total and completed bytes, and its completed parts or
    // ranges. It holds no credential or signed URL. It is not a durable record: a paused transfer resumes only through
    // the same live TransferJob, with its source or destination still open and unchanged.
    public function checkpoint()
    {
        return $this->transfer->checkpoint();
    }
}
