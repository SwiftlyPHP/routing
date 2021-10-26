<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Collection\RouteCollection;

/**
 * Interface for classes that can provide/load route definitions
 */
Interface ProviderInterface
{

    /**
     * Populate the collection with routes from this provider
     *
     * @psalm-flow ($collection) -> return
     *
     * @param RouteCollection $collection Route collection
     * @return RouteCollection            Updated collection
     */
    public function populate( RouteCollection $collection ) : RouteCollection;

}
