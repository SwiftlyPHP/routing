<?php

namespace Swiftly\Routing;

/**
 * Represents a variable placeholder/parameter in a route
 *
 * @psalm-immutable
 */
Interface ParameterInterface
{

    /**
     * Get the name of this parameter
     *
     * @return string Parameter name
     */
    public function name() : string;

    /**
     * Check to see if a value is of the expected type
     *
     * @psalm-param scalar $value
     *
     * @param mixed $value Parameter value
     * @return bool        Is valid?
     */
    public function validate( $value ) : bool;

    /**
     * Escapes a value for use in a URL
     *
     * @psalm-param scalar $value
     *
     * @param mixed $value Parameter value
     * @return string      Escaped value
     */
    public function escape( $value ) : string;

    /**
     * Return the regex used to match this parameter
     *
     * @return string Regex
     */
    public function regex() : string;

    /**
     * Return the regex used to match this parameter
     *
     * @see ParameterInterface::regex
     * @return string Regex
     */
    public function __toString();
}
