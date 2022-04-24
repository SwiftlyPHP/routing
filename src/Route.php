<?php

namespace Swiftly\Routing;

use Swiftly\Routing\ParameterInterface;
use Swiftly\Routing\Parameter\NumericParameter;
use Swiftly\Routing\Parameter\StringParameter;

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
     * The raw (unparsed) URL of this route
     *
     * @var string $url Route URL
     */
    public $url;

    /**
     * URL components for this route
     *
     * @psalm-var list<string|ParameterInterface> $parts
     *
     * @var string[]|ParameterInterface[] $parts URL components
     */
    private $components = [];

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
     * @param string $url       Route URL
     * @param callable $handler Route handler
     */
    public function __construct( string $url, $handler )
    {
        $this->url = $url;
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
        return strpos( $this->url, '[', ) === false;
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
            return $this->url;
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

        // Coerce any ParameterInterface into string
        foreach ( $this->components() as $component ) {
            $regex .= (string)$component;
        }

        return $regex;
    }

    /**
     * Parse the URL and generate the components of this route
     *
     * @psalm-external-mutation-free
     * @psalm-return list<string|ParameterInterface>
     *
     * @return string[]|ParameterInterface[] URL components
     */
    public function components() : array
    {
        if ( !empty( $this->components ) ) {
            return $this->components;
        }

        // Static routes are just a single component
        if ( $this->isStatic() ) {
            return [ $this->url ];
        }

        // Parse the dynamic portion of the route
        if ( !preg_match_all( self::ARGS_REGEX, $this->url, $matches, self::REGEX_FLAGS ) ) {
            // TODO: Throw maybe?
            return [ $this->url ];
        }

        foreach ( $matches as $match ) {
            $this->components[] = $this->component( $match );
        }

        return $this->components;
    }

    /**
     * Turn the regex captured component into the correct type
     *
     * @return string|ParameterInterface component
     */
    private function component( array $component ) // : string|ParameterInterface
    {
        if ( empty( $component['name'] ) ) {
            return preg_quote( $component[0][0] );
        }

        $name = $component['name'][0];

        switch ( $component['type'][0] ) {
            case 'i':
                $component = new NumericParameter( $name );
                break;
            case 's':
            default:
                $component = new StringParameter( $name );
                break;
        }

        $this->args[] = $name;

        return $component;
    }
}
