<?php

namespace Swiftly\Routing\Matcher;

use Swiftly\Routing\MatcherInterface;
use Swiftly\Routing\Collection;
use Swiftly\Routing\MatchedRoute;

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
        $matched = null;

        foreach ($this->routes->static() as $name => $route) {
            if ($route->supports($method) && $route->getComponent(0) === $url) {
                $matched = new MatchedRoute($name, $route);
                break;
            }
        }

        return $matched;
    }
}
