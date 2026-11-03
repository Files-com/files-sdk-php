<?php

declare(strict_types=1);

namespace Files\Test;

/**
 * A request of the default suite that no test handler was bound for (SuiteGuard).
 */
final class UnboundRequestException extends \LogicException
{
}
