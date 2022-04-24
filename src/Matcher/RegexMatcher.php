<?php

namespace Swiftly\Routing\Matcher;

use Swiftly\Routing\MatcherInterface;
use Swiftly\Routing\Collection;
use Swiftly\Routing\Route;

use function preg_match_all;

use const PREG_SET_ORDER;

/**
 * Class that uses regex to match a URL to a route
 */
Class RegexMatcher Implements MatcherInterface
{

    /**
     * @psalm-var array<string, Route> $routes
     *
     * @var array $routes Dynamic routes
     */
    private $routes;

    /**
     * @var string $regex Match expression
     */
    private $regex;

    /**
     * Create a new regex matcher using the routes and expression provided
     *
     * @psalm-param array<string, Route> $routes
     *
     * @param Route[] $routes Dynamic routes
     * @param string $regex   Match expression
     */
    public function __construct( array $routes, string $regex )
    {
        $this->routes = $routes;
        $this->regex = $regex;
    }

    /**
     * {@inheritdoc}
     */
    public function match( string $url ) : ?Route
    {
        if ( !preg_match_all( $this->regex, $url, $matches, PREG_SET_ORDER ) ) {
            return null;
        }

        // Get the named route
        $route = $this->routes[$matches[0]['MARK']];

        // Handle params (if any)
        $args = [];

        foreach ( $route->args as $index => $param ) {
            $args[$param] = $matches[0][$index + 1] ?? null;
        }

        $route->args = $args;

        return $route;
    }
}
