<?php

namespace Swiftly\Routing\Matcher;

use Swiftly\Routing\MatcherInterface;
use Swiftly\Routing\Collection\RouteCollection;
use Swiftly\Routing\Route;

/**
 * Class that matches URLs to static routes
 *
 * @author clvarley
 */
Class StaticMatcher Implements MatcherInterface
{

    /**
     * @var Route[] $routes Mapped routes
     */
    private $routes;

    /**
     * Create a new static matcher
     *
     * Takes an array where the keys are the static URL and the value is the
     * mapped `Route`.
     *
     * @psalm-param array<string,Route> $routes
     *
     * @param Route[] $routes Mapped routes
     */
    public function __construct( array $routes )
    {
        $this->routes = $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function match( string $url ) : ?Route
    {
        if ( !isset( $this->routes[$url] ) ) {
            return null;
        }

        return $this->routes[$url];
    }
}
