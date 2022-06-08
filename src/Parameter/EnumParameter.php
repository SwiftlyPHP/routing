<?php

namespace Swiftly\Routing\Parameter;

use Swiftly\Routing\ParameterInterface;

use function preg_quote;
use function implode;

/**
 * Represents a parameter that must be one of several options
 *
 * @psalm-immutable
 */
Class EnumParameter Implements ParameterInterface
{

    /** @var string $name */
    private $name;

    /** @var string[] $choices */
    private $choices;

    /**
     * Creates a new numeric parameter with the given name
     *
     * @param string $name      Parameter name
     * @param string[] $choices Valid choices
     */
    public function __construct( string $name, array $choices )
    {
        $this->name = $name;
        $this->choices = $choices;
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
        return in_array( $value, $this->choices );
    }

    /**
     * {@inheritdoc}
     */
    public function escape( $value ) : string
    {
        return (string)$value;
    }

    /**
     * {@inheritdoc}
     */
    public function regex() : string
    {
        $choices = [];

        foreach ( $this->choices as $choice ) {
            $choices[] = preg_quote( $choice );
        }

        return '(' . implode( '|', $choices ) . ')';
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->regex();
    }
}
