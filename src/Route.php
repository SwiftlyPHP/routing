<?php

namespace Swiftly\Routing;

use Swiftly\Routing\ParameterInterface;

use function in_array;
use function strpos;
use function preg_match_all;
use function preg_quote;

use const PREG_SET_ORDER;
use const PREG_OFFSET_CAPTURE;

/**
 * Simple class used to represent a single route
 */
Class Route
{

    /**
     * The regex used to strip out URL args
     *
     * @var string ARGS_REGEX Regular expression
     */
    const ARGS_REGEX = '~\[(?:(?P<type>i|s):)?(?P<name>\w+)\]|(?:[^\[]+)~ix';

    /**
     * Flags to be used on calls to `preg_match_all`
     *
     * @var int REGEX_FLAGS Flags
     */
    const REGEX_FLAGS = PREG_SET_ORDER | PREG_OFFSET_CAPTURE;

    /**
     * URL components for this route
     *
     * @psalm-var list<string|ParameterInterface> $parts
     *
     * @var string[]|ParameterInterface[] $parts URL components
     */
    public $components;

    /**
    * Function used to handle this route
    *
    * @var callable $handler Route controller
    */
    public $handler;

    /**
     * Allowed HTTP methods for this route
     *
     * @var string[] $methods HTTP methods
     */
    public $methods = [];

    /**
     * Arguments to be passed to the handler
     *
     * @var string[] $args Route arguments
     */
    public $args = [];

    /**
     * List of tags that apply to this route
     *
     * @var string[] $tags Route tags
     */
    public $tags = [];

    /**
     * Associate a handler with a route
     *
     * It is worth noting: we cannot use the `callable` typehint here as it
     * can cause "Non-static method" warnings when using the older `::` string
     * syntax.
     *
     * @psalm-param string|list<string|ParameterInterface> $url
     *
     * @param string|array $url URL components
     * @param callable $handler Route handler
     */
    public function __construct( $url, $handler )
    {
        $this->components = (array)$url;
        $this->handler = $handler;
    }

    /**
     * Check to see if this route supports the given method
     *
     * If the route has no HTTP methods defined, it is assumed that it should
     * respond to all requests.
     *
     * @psalm-mutation-free
     *
     * @param string $method HTTP method
     * @return bool          Supported method
     */
    public function supports( string $method ) : bool
    {
        return empty( $method ) || in_array( $method, $this->methods );
    }

    /**
     * Check to see if this route is static or dynamic
     *
     * @psalm-mutation-free
     *
     * @return bool Is static?
     */
    public function isStatic() : bool
    {
        return ( count( $this->components ) === 1
            && is_string( $this->components[0] )
        );
    }

    /**
     * Compile the regex used to match this route
     *
     * @return string Compiled regex
     */
    public function compile() : string
    {
        // Static not dynamic route?
        if ( $this->isStatic() ) {
            return $this->components[0];
        }

        return $this->build();
    }

    /**
     * Builds the regex for this route
     *
     * @return string Route regex
     */
    private function build() : string
    {
        $regex = '';

        // Something went wrong?
        if ( !preg_match_all( self::ARGS_REGEX, $this->url, $matches, self::REGEX_FLAGS ) ) {
            // TODO: Throw maybe?
            return $regex;
        }

        // Coerce any ParameterInterface into string
        foreach ( $this->components as $component ) {
            $regex .= (string)$component;
        }

        return $regex;
    }

    /**
     * Parse the arg data and build the regex
     *
     * @param array[] $arg Argument data
     * @return string      Argument regex
     */
    private function parseArg( array $arg ) : string
    {
        if ( empty( $arg['name'] ) ) {
            return preg_quote( $arg[0][0] );
        }

        switch ( $arg['type'][0] ) {
            case 'i':
                $regex = '\d+';
            break;

            case 's':
            default:
                $regex = '[a-zA-Z0-9-_]+';
            break;
        }

        $this->args[] = $arg['name'][0];

        return "($regex)";
    }
}
