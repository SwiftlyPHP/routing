<?php

namespace Swiftly\Routing\Collection;

use Iterator;

use function current;
use function key;
use function next;
use function reset;
use function array_udiff;

/**
 * Class used to represent a collection of objects
 *
 * @internal
 * @template TKey of array-key
 * @template TVal
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
     * @final
     * @param array<TKey,TVal> $items
     */
    final public function __construct( array $items = [] )
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
        return current( $this->items ) !== false;
    }

    /**
     * Check to see if this collection is empty
     *
     * @psalm-mutation-free
     *
     * @return bool Empty collection
     */
    public function empty() : bool
    {
        return empty( $this->items );
    }

    /**
     * Filters the collection using the provided callback
     *
     * @psalm-mutation-free
     * @psalm-param callable(TVal):bool $callback
     * @psalm-return static
     *
     * @param callable $callback Filter function
     * @return static            Filtered collection
     */
    public function filter( callable $callback ) // : static
    {
        $items = [];

        foreach ( $this->items as $key => $item ) {
            if ( $callback( $item ) ) {
                $items[$key] = $item;
            }
        }

        return new static( $items );
    }

    /**
     * Compute the difference between this and another collection
     *
     * @psalm-param self<TKey,TVal> $collection
     * @psalm-param ?callable(TVal,TVal):(-1|0|1) $comparator
     * @psalm-return static
     *
     * @param static $collection   Comparable collection
     * @param callable $comparator Comparison function
     * @return static              Computed difference
     */
    public function diff( self $collection, ?callable $comparator = null ) // : static
    {
        $comparator = $comparator ?: [ $this, 'compare' ];

        $difference = array_udiff( $this->items, $collection->items, $comparator );

        return new static( $difference );
    }

    /**
     * Fallback comparison functions for calls to `array_udiff`
     *
     * @psalm-mutation-free
     * @psalm-param TVal $val1
     * @psalm-param TVal $val2
     * @psalm-return -1|0|1
     *
     * @param mixed $val1 Comparison value
     * @param mixed $val2 Comparison value
     * @return int
     */
    protected function compare( $val1, $val2 ) : int
    {
        return $val1 <=> $val2;
    }
}
