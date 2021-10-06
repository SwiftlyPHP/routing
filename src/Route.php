<?php

namespace Swiftly\Routing;

use const PREG_SET_ORDER;
use const PREG_OFFSET_CAPTURE;

use function preg_match_all;
use function preg_quote;

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
     * The name of this route
     *
     * @var string $name Route name
     */
    public $name = '';

    /**
     * The raw path from the routes file
     *
     * @var string $raw Route URL
     */
    public $raw = '';

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
     * Arguments to be passed to the controller
     *
     * @var array $args Route arguments
     */
    public $args = [];

    /**
     * List of tags that apply to this route
     *
     * @var string[] $tags Route tags
     */
    public $tags = [];

    /**
     * The controller used to handle this route
     *
     * @var callable|null $callable Route controller
     */
    public $callable = null;

    /**
     * Compile the regex used to match this route
     *
     * @return string Compiled regex
     */
    public function compile() : string
    {
        if ( !empty( $this->regex ) ) {
            return $this->regex;
        }

        // Static not dynamic route
        if ( !preg_match_all( self::ARGS_REGEX, $this->raw, $matches, self::REGEX_FLAGS ) ) {
            $this->regex = $this->raw;

            return $this->regex;
        }

        // Build regex
        foreach ( $matches as $match ) {
            $this->regex .= $this->parseArg( $match );
        }

        return $this->regex;
    }

    /**
     * Parse the arg data and build the regex
     *
     * @psalm-param array{0:array{0:string},name:string,type:string} $arg
     *
     * @param string[] $arg Argument data
     * @return string       Argument regex
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
