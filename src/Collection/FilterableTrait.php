<?php

namespace Swiftly\Routing\Collection;

use Swiftly\Routing\Collection\GenericCollection;

/**
 * Trait allowing collections to be filtered
 *
 * @template TVal
 * @psalm-require-extends GenericCollection
 * @author clvarley
 */
Trait FilterableTrait
{

    /**
     * Filters the collection using the provided callback
     *
     * Collection keys are preserved.
     *
     * @psalm-mutation-free
     * @psalm-param callable(TVal):bool $callback
     *
     * @param callable $callback  Filter function
     * @return static             Filtered collection
     */
    public function filter( callable $callback ) // : static
    {
        $collection = [];

        foreach ( $this->items as $key => $item ) {
            if ( $callback( $item ) ) {
                $collection[$key] = $item;
            }
        }

        return new static( $collection );
    }
}
