<?php declare(strict_types=1);

namespace Swiftly\Routing;

use Swiftly\Routing\Exception\FormatException;

/**
 * Interface representing a flexible/variable part of a URL
 *
 * @psalm-immutable
 */
interface ComponentInterface
{
    /**
     * Return the name of this URL component
     *
     * @psalm-return non-empty-string
     *
     * @return string
     */
    public function name(): string;

    /**
     * Return the regex used to match values for this component
     *
     * @psalm-return non-empty-string
     *
     * @return string
     */
    public function regex(): string;

    /**
     * Determine if the given value is accepted by this component
     *
     * @param mixed $value Value to check
     */
    public function accepts($value): bool;

    /**
     * Attempt to escape the given value for use in a URL
     *
     * @throws FormatException If value cannot be escaped
     * @param mixed $value     Value to format
     * @return string
     */
    public function escape($value): string;
}
