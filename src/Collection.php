<?php declare(strict_types=1);

namespace Swiftly\Routing;

use Swiftly\Routing\Route;

use function array_filter;

/**
 * Used to store and manage a collection of routes
 *
 * @internal
 *
 * @psalm-immutable
 */
class Collection
{
    /**
     * Create a new collection around the given routes
     *
     * @param array<non-empty-string,Route> $routes
     */
    public function __construct(
        private array $routes,
    ) {
    }

    /**
     * Determine if a named route exists within the collection
     *
     * @param non-empty-string $name Route name
     */
    public function has(string $name): bool
    {
        return isset($this->routes[$name]);
    }

    /**
     * Return the named route from the collection
     *
     * @param non-empty-string $name
     */
    public function get(string $name): ?Route
    {
        return $this->routes[$name] ?? null;
    }

    /**
     * Return all static routes from the collection
     *
     * @return array<non-empty-string,Route>
     */
    public function static(): array
    {
        return array_filter(
            $this->routes,
            static fn (Route $route): bool => $route->isStatic(),
        );
    }

    /**
     * Return all dynamic routes from the collection
     *
     * @return array<non-empty-string,Route>
     */
    public function dynamic(): array
    {
        return array_filter(
            $this->routes,
            static fn (Route $route): bool => !$route->isStatic(),
        );
    }

    /**
     * Return all the routes stored in this collection
     *
     * @return array<non-empty-string,Route>
     */
    public function all(): array
    {
        return $this->routes;
    }
}
