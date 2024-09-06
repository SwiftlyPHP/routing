<?php declare(strict_types=1);

namespace Swiftly\Routing\Exception;

use LogicException;

use function sprintf;

/**
 * Exception to be thrown when a required route argument is missing
 */
class MissingArgumentException extends LogicException
{
    /**
     * Indicate the named `$argument` was not supplied
     *
     * @param string $argument Argument name
     */
    public function __construct(string $argument)
    {
        parent::__construct(
            sprintf(
                "Route requires argument '%s' but it was not supplied",
                $argument
            )
        );
    }
}
