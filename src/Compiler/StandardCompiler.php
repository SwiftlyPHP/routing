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
 *
 * @psalm-immutable
 */
Class StandardCompiler Implements CompilerInterface
{

    /**
     * {@inheritdoc}
     */
    public function compile( Collection $routes ) : MatcherInterface
    {
        $matchers = [];

        // Get the static routes
        $static = $routes->filter( function ( Route $route ) : bool {
            return strpos( $route->url, '[', 1 ) === false;
        });

        if ( !$static->isEmpty() ) {
            $matchers[] = $this->handleStatic( $static );
        }

        // Get remaining routes
        $dynamic = $routes->diff( $static );

        if ( !$dynamic->isEmpty() ) {
            $matchers[] = $this->handleRegex( $dynamic );
        }

        return new AggregateMatcher( $matchers );
    }

    /**
     * Creates a new StaticMatcher
     *
     * @param Collection $routes Static routes
     * @return StaticMatcher     Static route matcher
     */
    private function handleStatic( Collection $routes ) : StaticMatcher
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
     * @param Collection $routes Dynamic routes
     * @return RegexMatcher      Dynamic route matcher
     */
    private function handleRegex( Collection $routes ) : RegexMatcher
    {
        $regexes = [];

        foreach ( $routes as $name => $route ) {
            $regexes[] = '(?>' . $route->compile() . '(*:' . $name . '))';
        }

        $regex = '~^(?|' . implode( '|', $regexes ) . ')$~ixX';

        return new RegexMatcher( $routes, $regex );
    }
}
