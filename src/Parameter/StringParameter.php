<?php

namespace Swiftly\Routing\Parameter;

use Swiftly\Routing\ParameterInterface;

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
        return !empty( $value );
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
}
