<?php

namespace Swiftly\Routing\Parameter;

use Swiftly\Routing\ParameterInterface;

/**
 * Represents a simple string URL parameter
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
    public function validate( string $value ) : bool
    {
        // TODO: Could do some URL validation here?
        return !empty( $value );
    }

    /**
     * {@inheritdoc}
     */
    public function escape( string $value ) : string
    {
        // TODO: Could filter out invalid chars here?
        return $value;
    }
}
