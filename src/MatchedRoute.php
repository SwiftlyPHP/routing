<?php declare(strict_types=1);

namespace Swiftly\Routing;

use Swiftly\Routing\Route;

/**
 * Plain old data structure used to store information about a matched route
 *
 * @internal
 *
 * @psalm-immutable
 *
 * @upgrade:php8.1 Mark properties readonly
 */
final class MatchedRoute
{
    /**
     * Associate a route with the given name and arguments
     *
     * @param non-empty-string $name     Route name
     * @param Route $route               Matched route
     * @param array<string,string> $args Matched arguments
     */
    public function __construct(
        public string $name,
        public Route $route,
        public array $args = [],
    ) {
    }
}
