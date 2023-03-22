<?php

namespace Swiftly\Routing\Component;

use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Exception\FormatException;
use Stringable;

use function array_map;
use function implode;
use function strtolower;
use function in_array;
use function is_scalar;

/**
 *
 *
 * @psalm-immutable
 */
Final Class EnumComponent Implements ComponentInterface
{
    /**
     * @psalm-var non-empty-string $name
     * @var string $name
     */
    private $name;

    /**
     * @psalm-var list<string> $allowed
     * @var string[] $allowed
     */
    private $allowed;

    /**
     * Create a new enum URL component that accepts the `$allowed` values
     *
     * @psalm-param non-empty-string $name
     * @psalm-param list<string> $allowed
     *
     * @param string $name      Component name
     * @param string[] $allowed Allowed enum values
     */
    public function __construct(string $name, array $allowed)
    {
        $this->name = $name;
        $this->allowed = $allowed;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function regex(): string
    {
        $allowed = array_map('preg_quote', $this->allowed);
        $allowed = implode('|', $allowed);

        return "($allowed)";
    }

    public function accepts($value): bool
    {
        if (!$this->isStringable($value)) {
            return false;
        }

        $value = (string)$value;
        $value = strtolower($value);

        return in_array($value, array_map('strtolower', $this->allowed), true);
    }

    public function escape($value): string
    {
        if (!$this->accepts($value)) {
            throw new FormatException($this->name(), $value);
        }

        return (string)$value;
    }

    /**
     * Determine if the given variable can be casted into a string
     *
     * @psalm-assert-if-true scalar|Stringable $value
     * 
     * @param mixed $value Subject variable
     * @return bool
     */
    private function isStringable($value): bool
    {
        return (is_scalar($value) || $value instanceof Stringable);
    }
}
