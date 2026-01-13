<?php declare(strict_types=1);

namespace Swiftly\Routing\Component;

use Stringable;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Exception\FormatException;

use function array_map;
use function implode;
use function in_array;
use function is_scalar;
use function strtolower;

/**
 * Component representing a variable restricted to a set of allowed values
 *
 * @psalm-immutable
 */
final class EnumComponent implements ComponentInterface
{
    /**
     * Create a new enum URL component that accepts the `$allowed` values
     *
     * @psalm-param non-empty-string $name
     * @psalm-param list<string> $allowed
     *
     * @param non-empty-string $name Component name
     * @param list<string> $allowed  Allowed enum values
     */
    public function __construct(
        private string $name,
        private array $allowed,
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
        $allowed = array_map('preg_quote', $this->allowed);
        $allowed = implode('|', $allowed);

        return "($allowed)";
    }

    /** {@inheritDoc} */
    public function accepts($value): bool
    {
        if (!self::isStringable($value)) {
            return false;
        }

        $value = (string) $value;
        $value = strtolower($value);

        return in_array($value, array_map('strtolower', $this->allowed), true);
    }

    /** {@inheritDoc} */
    public function escape($value): string
    {
        if (!$this->accepts($value)) {
            throw new FormatException($this->name(), $value);
        }

        return (string) $value;
    }

    /**
     * Determine if the given variable can be casted into a string
     *
     * @psalm-assert-if-true scalar|Stringable $value
     *
     * @param mixed $value Subject variable
     * @return bool        Subject can be represented as a string?
     */
    private static function isStringable($value): bool
    {
        return is_scalar($value) || $value instanceof Stringable;
    }
}
