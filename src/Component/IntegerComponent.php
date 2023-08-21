<?php

namespace Swiftly\Routing\Component;

use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Exception\FormatException;

use function filter_var;

use const FILTER_VALIDATE_INT;

/**
 * Component requiring a variable to be a valid integer
 *
 * @psalm-immutable
 */
class IntegerComponent implements ComponentInterface
{
    /** @psalm-var non-empty-string $name */
    private string $name;

    /**
     * Create a new numeric only URL component
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
        return "(\d+)";
    }

    /** {@inheritDoc} */
    public function accepts($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
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
