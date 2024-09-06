<?php declare(strict_types=1);

namespace Swiftly\Routing\Exception;

use DomainException;

use function sprintf;
use function is_scalar;
use function gettype;

/**
 * Exception to be thrown when a value cannot be used as a route argument
 */
class FormatException extends DomainException
{
    /**
     * Indicate the `$provided` value could not be used for the route argument
     *
     * @param string $argument Argument name
     * @param mixed $provided  Provided value
     */
    public function __construct(string $argument, $provided)
    {
        parent::__construct(
            sprintf(
                "Given value of '%s' is not supported by route argument '%s'",
                self::typeToString($provided),
                $argument
            )
        );
    }

    /**
     * @param mixed $value Value to string-ify
     */
    private static function typeToString($value): string
    {
        return (is_scalar($value) ? (string)$value : gettype($value));
    }
}
