<?php

namespace Swiftly\Routing\Compiler;

use Swiftly\Routing\CompilerInterface;
use Swiftly\Routing\Collection;
use Swiftly\Routing\MatcherInterface;
use Swiftly\Routing\Route;
use Swiftly\Routing\Matcher\AggregateMatcher;
use Swiftly\Routing\Matcher\StaticMatcher;
use Swiftly\Routing\Matcher\RegexMatcher;

use function strpos;
use function implode;

/**
 * Standard compiler that can handle both static and dynamic routes
 */
Class StandardCompiler Implements CompilerInterface
{

    /**
     * {@inheritdoc}
     */
    public function compile( Collection $routes ) : MatcherInterface
    {
        $matchers = [];
        $static = [];
        $dynamic = [];

        foreach ( $routes->all() as $name => $route ) {
            if ( $route->isStatic() ) {
                $static[$name] = $route;
            } else {
                $dynamic[$name] = $route;
            }
        }

        // Get the static routes
        if ( !empty( $static ) ) {
            $matchers[] = $this->handleStatic( $static );
        }

        // Get remaining routes
        if ( !empty( $dynamic ) ) {
            $matchers[] = $this->handleRegex( $dynamic );
        }

        return new AggregateMatcher( $matchers );
    }

    /**
     * Creates a new StaticMatcher
     *
     * @psalm-param array<string, Route> $routes
     *
     * @param Route[] $routes Static routes
     * @return StaticMatcher  Static route matcher
     */
    private function handleStatic( array $routes ) : StaticMatcher
    {
        $mapping = [];

        foreach ( $routes as $route ) {
            $mapping[$route->url] = $route;
        }

        return new StaticMatcher( $mapping );
    }

    /**
     * Creates a new RegexMatcher
     *
     * @psalm-param array<string, Route> $routes
     *
     * @param Route[] $routes Dynamic routes
     * @return RegexMatcher      Dynamic route matcher
     */
    private function handleRegex( array $routes ) : RegexMatcher
    {
        $regexes = [];

        foreach ( $routes as $name => $route ) {
            $regexes[] = '(?>' . $route->compile() . '(*:' . $name . '))';
        }

        $regex = '~^(?|' . implode( '|', $regexes ) . ')$~ixX';

        return new RegexMatcher( $routes, $regex );
    }
}
