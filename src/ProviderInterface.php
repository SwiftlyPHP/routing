<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Collection;

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
     * @param Collection $collection Route collection
     * @return Collection            Updated collection
     */
    public function populate( Collection $collection ) : Collection;

}
