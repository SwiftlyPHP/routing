<?php declare(strict_types=1);

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
final class IntegerComponent implements ComponentInterface
{
    /**
     * Create a new numeric only URL component
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
        return "(\d+)";
    }

    /** {@inheritDoc} */
    public function accepts(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /** {@inheritDoc} */
    public function escape(mixed $value): string
    {
        if (!$this->accepts($value)) {
            throw new FormatException($this->name(), $value);
        }

        return (string)$value;
    }
}
