<?php declare(strict_types=1);

namespace Swiftly\Routing;

use Swiftly\Routing\Exception\InvalidArgumentException;
use Swiftly\Routing\Exception\MissingArgumentException;
use Swiftly\Routing\Exception\UndefinedRouteException;

/**
 * Interface for classes capable of generating a URL for a named route.
 */
interface UrlGeneratorInterface
{
    /**
     * Generate a URL for the named route.
     *
     * @throws UndefinedRouteException
     *          If the given route can't be found
     * @throws MissingArgumentException
     *          If a required route argument is missing
     * @throws InvalidArgumentException
     *          If a given route argument is invalid
     *
     * @param non-empty-string $name     Route name
     * @param array<string, mixed> $args Route arguments
     * @return string                    Generated URL
     */
    public function generate(string $name, array $args): string;
}
