<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Collection;
use Swiftly\Routing\MatcherInterface;

/**
 * Interface for classes that can compile/create route matchers
 */
Interface CompilerInterface
{

    /**
     * Compile the appropriate matcher for the given routes
     *
     * @param Collection $routes Route collection
     * @return MatcherInterface  Compiled matcher
     */
    public function compile( Collection $routes ) : MatcherInterface;

}
