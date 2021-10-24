<?php

namespace Swiftly\Routing\Collection;

use Iterator;

use function current;
use function key;
use function next;
use function reset;

/**
 * Class used to represent a collection of objects
 *
 * @internal
 * @template TKey as array-key
 * @template TVal
 * @author clvarley
 */
Class GenericCollection Implements Iterator
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
    public function current() /* : mixed */
    {
        return current( $this->items );
    }

    /**
     * Get the key of the current element
     *
     * @return TKey
     */
    public function key() /* : mixed */
    {
        return key( $this->items );
    }

    /**
     * Move to the next element
     */
    public function next() : void
    {
        next( $this->items );
    }

    /**
     * Rewind to the first element
     */
    public function rewind() : void
    {
        reset( $this->items );
    }

    /**
     * Check if the current position is valid
     *
     * @return bool Valid position
     */
    public function valid() : bool
    {
        return current( $this->items ) !== null;
    }
}
