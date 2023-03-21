<?php

namespace Swiftly\Routing\Component;

use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Exception\FormatException;

use function is_string;
use function preg_match;

/**
 * 
 *
 * @psalm-immutable
 */
class StringComponent implements ComponentInterface
{
    /**
     * @psalm-param non-empty-string $name
     * @param string $name
     */
    private $name;

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

    public function name(): string
    {
        return $this->name;
    }

    public function regex(): string
    {
        return "([A-Za-z0-9\-\_\@\.]+)";
    }

    public function accepts($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match("/^{$this->regex()}$/", $value) === 1;
    }

    public function escape($value): string
    {
        if (!$this->accepts($value)) {
            throw new FormatException($this->name(), $value);
        }

        return (string)$value;
    }
}
