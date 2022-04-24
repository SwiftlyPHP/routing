<?php

namespace Swiftly\Routing\Parameter;

use Swiftly\Routing\ParameterInterface;

use function is_numeric;
use function intval;

/**
 * Represents a simple numeric URL parameter
 *
 * @psalm-immutable
 */
Class NumericParameter Implements ParameterInterface
{

    /** @var string $name */
    private $name;

    /**
     * Creates a new numeric parameter with the given name
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
        return is_numeric( $value );
    }

    /**
     * {@inheritdoc}
     */
    public function escape( $value ) : string
    {
        return (string)intval( $value );
    }

    /**
     * {@inheritdoc}
     */
    public function regex() : string
    {
        return '(\d+)';
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->regex();
    }
}
