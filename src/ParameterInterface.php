<?php

namespace Swiftly\Routing;

/**
 * Represents a variable placeholder/parameter in a route
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
     * @param string $value Parameter value
     * @return bool         Is valid?
     */
    public function validate( string $value ) : bool;

    /**
     * Escapes a value for use in a URL
     *
     * @param string $value Parameter value
     * @return string       Escaped value
     */
    public function escape( string $value ) : string;

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
