<?php declare(strict_types=1);

namespace Swiftly\Routing\Component;

use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Exception\FormatException;

use function is_string;
use function preg_match;

/**
 * Component that accepts a string of URL allowed characters
 *
 * @psalm-immutable
 */
final class StringComponent implements ComponentInterface
{
    /**
     * Create a new URL component
     *
     * @param non-empty-string $name Component name
     */
    public function __construct(
        private string $name,
    ) {
    }

    /** {@inheritDoc} */
    public function name(): string
    {
        return $this->name;
    }

    /** {@inheritDoc} */
    public function regex(): string
    {
        return "([A-Za-z0-9\-\_\@\.]+)";
    }

    /** {@inheritDoc} */
    public function accepts(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match("/^{$this->regex()}$/", $value) === 1;
    }

    /** {@inheritDoc} */
    public function escape(mixed $value): string
    {
        if (!$this->accepts($value)) {
            throw new FormatException($this->name(), $value);
        }

        return (string) $value;
    }
}
