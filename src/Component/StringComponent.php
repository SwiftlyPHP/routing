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
class StringComponent implements ComponentInterface
{
    /** @psalm-var non-empty-string $name */
    private string $name;

    /**
     * Create a new URL component
     *
     * @psalm-param non-empty-string $name
     *
     * @param string $name      Component name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
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
    public function accepts($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match("/^{$this->regex()}$/", $value) === 1;
    }

    /** {@inheritDoc} */
    public function escape($value): string
    {
        if (!$this->accepts($value)) {
            throw new FormatException($this->name(), $value);
        }

        return (string)$value;
    }
}
