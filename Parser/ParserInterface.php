<?php

namespace Swiftly\Routing\Parser;

use Swiftly\Routing\Collection\CollectionInterface;

/**
 * Interface for classes that parse route files
 *
 * @author <clvarley>
 */
Interface ParserInterface
{

    /**
     * Parse the given routes file and return an array of routes
     *
     * @param string $filename                          Path to file
     * @return CollectionInterface                      Route collection
     */
    public function parse( string $filename ) : CollectionInterface;

}
