<?php declare(strict_types=1);

namespace Swiftly\Routing;

use Swiftly\Routing\ComponentInterface;

/**
 * Capable of parsing a path into a sequence of route components
 *
 * @psalm-immutable
 */
interface ParserInterface
{
    /**
     * Parse a URL path into relevant components
     *
     * @psalm-param non-empty-string $path
     * @psalm-return non-empty-list<string|ComponentInterface>
     *
     * @param string $path                      URL path to parse
     * @return array<string|ComponentInterface> Components
     */
    public function parse(string $path): array;
}
