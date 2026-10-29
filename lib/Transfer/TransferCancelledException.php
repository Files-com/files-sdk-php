<?php

declare(strict_types=1);

namespace Files\Transfer;

use Files\Exception\FilesException;

/**
 * What TransferJob::wait throws for a job that TransferJob::cancel stopped before every transfer ended. A failure or a
 * callback's exception that came first is thrown instead, as it was.
 */
class TransferCancelledException extends FilesException
{
}
