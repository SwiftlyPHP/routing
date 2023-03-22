<?php

namespace Swiftly\Routing;

/**
 * Implemented by classes capable of providing a collection of routes
 */
interface ProviderInterface
{
    /**
     * Provide a collection
     */
    public function provide(): array;
}
