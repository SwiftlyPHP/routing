<?php declare(strict_types=1);

namespace Swiftly\Routing\Matcher;

use Swiftly\Routing\Collection;
use Swiftly\Routing\MatchedRoute;
use Swiftly\Routing\MatcherInterface;

/**
 * Matches against static routes
 *
 * @psalm-immutable
 */
class StaticMatcher implements MatcherInterface
{
    private Collection $routes;

    /**
     * Create a new static matcher around the given routes
     *
     * @param Collection $routes Registered routes
     */
    public function __construct(Collection $routes)
    {
        $this->routes = $routes;
    }

    public function match(string $url, string $method = 'GET'): ?MatchedRoute
    {
        foreach ($this->routes->static() as $name => $route) {
            if ($route->supports($method) && $route->getComponent(0) === $url) {
                return new MatchedRoute($name, $route);
            }
        }

        return null;
    }
}
