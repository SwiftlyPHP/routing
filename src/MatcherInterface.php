<?php

namespace Swiftly\Routing;

use Swiftly\Routing\MatchedRoute;

/**
 * Class capable of taking a URL and matching it against known routes
 */
interface MatcherInterface
{
    /**
     * Attempt to find a route that matches the given URL path
     * 
     * @param string $url        URL path
     * @return MatchedRoute|null Matched route
     */
    public function match(string $url): ?MatchedRoute;
}
