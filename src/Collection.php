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
    /** @var array<non-empty-string,Route> $routes */
    private array $routes;

    /**
     * Create a new collection around the given routes
     *
     * @param array<non-empty-string,Route> $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Determine if a named route exists within the collection
     *
     * @psalm-param non-empty-string $name
     *
     * @param string $name Route name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->routes[$name]);
    }

    /**
     * Return the named route from the collection
     *
     * @psalm-param non-empty-string $name
     *
     * @param string $name Route name
     * @return Route|null
     */
    public function get(string $name): ?Route
    {
        return $this->routes[$name] ?? null;
    }

    /**
     * Return all static routes from the collection
     *
     * @psalm-return array<non-empty-string,Route>
     *
     * @return Route[]
     */
    public function static(): array
    {
        return array_filter($this->routes, function (Route $route): bool {
            return $route->isStatic();
        });
    }

    /**
     * Return all dynamic routes from the collection
     *
     * @psalm-return array<non-empty-string,Route>
     *
     * @return Route[]
     */
    public function dynamic(): array
    {
        return array_filter($this->routes, function (Route $route): bool {
            return !$route->isStatic();
        });
    }

    /**
     * Return all the routes stored in this collection
     *
     * @psalm-return array<non-empty-string,Route>
     *
     * @return Route[]
     */
    public function all(): array
    {
        return $this->routes;
    }
}
