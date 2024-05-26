<?php

namespace Swiftly\Routing;

use Swiftly\Routing\MatchedRoute;

/**
 * Class capable of taking a URL and matching it against known routes
 */
interface MatcherInterface
{
    /**
     * Attempt to find a route that matches the given URL path.
     *
     * Can provide a `$method` to filter by routes that support a given HTTP
     * verb.
     *
     * @param string $url              URL path
     * @param non-empty-string $method Request method
     * @return MatchedRoute|null       Matched route
     */
    public function match(string $url, string $method = 'GET'): ?MatchedRoute;
}
