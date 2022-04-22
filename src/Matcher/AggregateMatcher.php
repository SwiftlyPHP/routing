<?php

namespace Swiftly\Routing\Matcher;

use Swiftly\Routing\MatcherInterface;
use Swiftly\Routing\Route;

/**
 * Class that calls matchers in series until a route is found
 *
 * @psalm-immutable
 */
Class AggregateMatcher Implements MatcherInterface
{

    /** @var MatcherInterface[] $matchers Route matchers */
    private $matchers;

    /**
     * Creates a new aggregator around the given matchers
     *
     * @param MatcherInterface[] $matchers Route matchers
     */
    public function __construct( array $matchers )
    {
        $this->matchers = $matchers;
    }

    /**
     * {@inheritdoc}
     */
    public function match( string $url ) : ?Route
    {
        foreach ( $this->matchers as $matcher ) {
            $route = $matcher->match( $url );

            if ( $route !== null ) {
                return $route;
            }
        }

        return null;
    }
}
