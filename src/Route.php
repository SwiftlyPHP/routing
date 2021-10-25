<?php

namespace Swiftly\Routing;

use function strpos;
use function preg_match_all;
use function preg_quote;

use const PREG_SET_ORDER;
use const PREG_OFFSET_CAPTURE;

/**
 * Simple class used to represent a single route
 *
 * @author clvarley
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
     * The raw path from the routes file
     *
     * @var string $raw Route URL
     */
    public $raw;

    /**
    * Function used to handle this route
    *
    * @var callable $handler Route controller
    */
    public $handler;

    /**
     * The regex used to match this route
     *
     * @var string $regex Compiled regex
     */
    public $regex = '';

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
     * It is worth noting: we cannot use the `callable` typehint here is it
     * can cause "Non-static method" warnings when using the older :: string
     * syntax.
     *
     * @param string $route     Route URL
     * @param callable $handler Route handler
     */
    public function __construct( string $route, $handler )
    {
        $this->raw = $route;
        $this->handler = $handler;
    }

    /**
     * Compile the regex used to match this route
     *
     * @return string Compiled regex
     */
    public function compile() : string
    {
        // Already compiled
        if ( !empty( $this->regex ) ) {
            return $this->regex;
        }

        // Static not dynamic route?
        if ( strpos( $this->raw, '[', 1 ) === false ) {
            return $this->raw;
        }

        // Does it look URL-like?
        $this->regex = $this->parseUrl();

        return $this->regex;
    }

    /**
     * Parse the URL into the regex
     *
     * @return string Route regex
     */
    private function parseUrl() : string
    {
        $regex = '';

        // Something went wrong?
        if ( !preg_match_all( self::ARGS_REGEX, $this->raw, $matches, self::REGEX_FLAGS ) ) {
            // TODO: Throw maybe?
            return $regex;
        }

        // Build regex
        foreach ( $matches as $match ) {
            $regex .= $this->parseArg( $match );
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
