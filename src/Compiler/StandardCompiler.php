<?php

namespace Swiftly\Routing\Compiler;

use Swiftly\Routing\CompilerInterface;
use Swiftly\Routing\Collection\RouteCollection;
use Swiftly\Routing\MatcherInterface;
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
    public function compile( RouteCollection $routes ) : MatcherInterface
    {
        $matchers = [];

        if (( $static = $this->handleStatic( $routes )) !== null ) {
            $matchers[] = $static;
        }

        if (( $regex = $this->handleRegex( $routes )) !== null ) {
            $matchers[] = $regex;
        }

        return new AggregateMatcher( $matchers );
    }

    /**
     * Creates a new StaticMatcher if any static routes are found
     *
     * @param RouteCollection $routes Route collection
     * @return StaticMatcher|null     Static route matcher
     */
    private function handleStatic( RouteCollection $routes ) : ?StaticMatcher
    {
        $mapping = [];

        foreach ( $routes as $route ) {
            if ( strpos( $route->url, '[', 1 ) === false ) {
                $mapping[$route->url] = $route;
            }
        }

        return empty( $mapping ) ? null : new StaticMatcher( $mapping );
    }

    /**
     * Creates a new RegexMatcher if any dynamic routes are found
     *
     * @param RouteCollection $routes Route collection
     * @return RegexMatcher|null      Dynamic route matcher
     */
    private function handleRegex( RouteCollection $routes ) : ?RegexMatcher
    {
        $regexes = [];

        foreach ( $routes as $name => $route ) {
            $regexes[] = '(?>' . $route->compile() . '(*:' . $name . '))';
        }

        if ( empty( $regexes ) ) {
            return null;
        }

        $regex = '~^(?|' . implode( '|', $regexes ) . ')$~ixX';

        return new RegexMatcher( $routes, $regex );
    }
}
