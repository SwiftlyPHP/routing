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
     * @return non-empty-string
     */
    public function name(): string;

    /**
     * Return the regex used to match values for this component
     *
     * @return non-empty-string
     */
    public function regex(): string;

    /**
     * Determine if the given value is accepted by this component
     */
    public function accepts(mixed $value): bool;

    /**
     * Attempt to escape the given value for use in a URL
     *
     * @throws FormatException If value cannot be escaped
     */
    public function escape(mixed $value): string;
}
