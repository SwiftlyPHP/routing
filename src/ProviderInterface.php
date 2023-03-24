<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Collection;

/**
 * Implemented by classes capable of providing a collection of routes
 */
interface ProviderInterface
{
    /**
     * Provide a collection
     */
    public function provide(): Collection;
}
