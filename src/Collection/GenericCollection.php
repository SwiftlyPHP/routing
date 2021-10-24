<?php

namespace Swiftly\Routing\Collection;

use Iterator;

/**
 * Class used to represent a collection of objects
 *
 * @internal
 * @template TKey as array-key
 * @template TVal
 * @author clvarley
 */
Class GenericCollecion Implements Iterator
{

    /**
     * The underlying items of this collection
     *
     * @var array<TKey,TVal> $items
     */
    protected $items;

    /**
     * Create a new collection over the given items
     *
     * @param array<TKey,TVal> $items
     */
    public function __construct( array $items = [] )
    {
        $this->items = $items;
    }

    /**
     * Get the current element
     *
     * @return TVal
     */
    public function current() /* : mixed */;

    /**
     * Get the key of the current element
     *
     * @return TKey
     */
    public function key() /* : mixed */;

    /**
     * Move to the next element
     */
    public function next() : void;

    /**
     * Rewind to the first element
     */
    public function rewind() : void;

    /**
     * Check if the current position is valid
     *
     * @return bool Valid position
     */
    public function valid() : void;
    
}
