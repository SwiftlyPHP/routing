<?php

namespace Swiftly\Routing\Parameter;

use Swiftly\Routing\ParameterInterface;

use function is_scalar;
use function is_object;
use function method_exists;

/**
 * Represents a simple string URL parameter
 *
 * @psalm-immutable
 */
Class StringParameter Implements ParameterInterface
{

    /** @var string $name */
    private $name;

    /**
     * Creates a new string parameter with the given name
     *
     * @param string $name Parameter name
     */
    public function __construct( string $name )
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function name() : string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function validate( $value ) : bool
    {
        // TODO: Could do some URL validation here?
        return ( !empty( $value )
            && ( is_scalar( $value )
                || ( is_object( $value ) && $this->isStringable( $value ) )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function escape( $value ) : string
    {
        // TODO: Could filter out invalid chars here?
        return (string)$value;
    }

    /**
     * {@inheritdoc}
     */
    public function regex() : string
    {
        return '([a-zA-Z0-9-_]+)';
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->regex();
    }

    /**
     * Determine if an object implements Stringable or has a toString method
     *
     * @psalm-assert-if-true \Stringable $object
     *
     * @param object $object Subject object
     * @return bool          Is stringable?
     */
    private function isStringable( object $object ) : bool
    {
        return method_exists( $object, '__toString' );
    }
}
