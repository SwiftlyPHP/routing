<?php declare(strict_types=1);

namespace Swiftly\Routing\Exception;

use InvalidArgumentException as BaseException;

use function sprintf;
use function gettype;

/**
 * Exception to be thrown when a given route argument is the wrong type
 */
class InvalidArgumentException extends BaseException
{
    /**
     * Indicate the named `$argument` was of the wrong type
     *
     * @param string $argument Argument name
     * @param mixed $provided  Provided value
     */
    public function __construct(string $argument, $provided)
    {
        parent::__construct(
            sprintf(
                "Invalid value provided for route argument '%s', recieved '%s'",
                $argument,
                gettype($provided)
            )
        );
    }
}
