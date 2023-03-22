<?php

namespace Swiftly\Routing\Component;

use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Exception\FormatException;

use function filter_var;

use const FILTER_VALIDATE_INT;

/**
 * 
 *
 * @psalm-immutable
 */
class IntegerComponent implements ComponentInterface
{
    /**
     * @psalm-var non-empty-string $name
     * @var string $name
     */
    private $name;

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

    public function name(): string
    {
        return $this->name;
    }

    public function regex(): string
    {
        return "(\d+)";
    }

    public function accepts($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public function escape($value): string
    {
        if (!$this->accepts($value)) {
            throw new FormatException($this->name(), $value);
        }

        return (string)$value;
    }
}
