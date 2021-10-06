<?php

namespace Swiftly\Routing\Parser;

use Swiftly\Routing\Collection\RouteCollection;
use Swiftly\Routing\CollectionInterface;
use Swiftly\Routing\ParserInterface;
use Swiftly\Routing\Route;

use function rtrim;
use function is_readable;
use function is_array;
use function file_get_contents;
use function array_filter;
use function array_intersect;
use function is_string;
use function explode;
use function json_decode;
use function json_last_error;
use function preg_match_all;
use function preg_quote;

use const JSON_ERROR_NONE;
use const PREG_SET_ORDER;
use const PREG_OFFSET_CAPTURE;

/**
 * Class responsible for loading routes from .json files
 *
 * @author clvarley
 */
Class JsonParser Implements ParserInterface
{

    /**
     * Allowed HTTP methods
     *
     * @var string[] HTTP_METHODS HTTP verbs
     */
    const HTTP_METHODS = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'UPDATE'
    ];

    /**
     * Parse the given json routes file
     *
     * @param string $filename Path to file
     * @return RouteCollection Route collection
     */
    public function parse( string $filename ) : CollectionInterface
    {
        $routes = new RouteCollection();

        $json = $this->loadJson( $filename );

        // Nothing to do
        if ( empty( $json ) ) {
            return $routes;
        }

        foreach ( $json as $name => $contents ) {
            if ( empty( $contents['path'] ) || empty( $contents['handler'] ) ) {
                continue;
            }

            // Create route definition struct
            $route = $this->convert( $contents );
            $route->name = (string)$name;

            $routes->add( $route->name, $route );
        }

        return $routes;
    }

    /**
     * Attempts to load the given JSON file
     *
     * @param string $filename JSON file
     * @return array           JSON data
     */
    private function loadJson( string $filename ) : array
    {
        if ( !is_readable( $filename ) ) {
            return [];
        }

        $raw = file_get_contents( $filename );

        // Nothing here, exit out
        if ( empty( $raw ) ) {
            return [];
        }

        /** @var array|false $json */
        $json = json_decode( $raw, true, 4 );

        // Parse error?
        if ( json_last_error() !== JSON_ERROR_NONE || !is_array( $json ) ) {
            $json = [];
        }

        return $json;
    }

    /**
     * Converts the JSON array into a Route object
     *
     * @param array $json Route JSON
     * @return Route      Route definition
     */
    private function convert( array $json ) : Route
    {
        $route = new Route;

        // Parse regex
        $route->regex = $this->compileRegex( $json['path'], $route );

        // Allowed HTTP verbs only
        if ( !empty( $json['methods'] ) && is_array( $json['methods'] ) ) {
            $route->methods = array_intersect( $json['methods'], self::HTTP_METHODS );
        } else {
            $route->methods = [ 'GET' ];
        }

        // Has tags?
        if ( !empty( $json['tags'] ) && is_array( $json['tags'] ) ) {
            $route->tags = array_filter( $json['tags'], 'is_string' );
        }

        // Controller/handler function
        if ( is_string( $json['handler'] ) ) {
            $route->callable = explode( '::', $json['handler'] );
        } else {
            $route->callable = $json['handler'];
        }

        return $route;
    }

    /**
     * Parse route and build the neccessary regex
     *
     * @param string $path Route path
     * @param Route $route Current route
     * @return string      Compiled regex
     */
    private function compileRegex( string $path, Route $route ) : string
    {
        $path = rtrim( $path, " \n\r\t\0\x0B\\/" );

        // No route, assume root
        if ( empty( $path ) ) {
            return '/';
        }

        // Gather any route placeholders?
        if ( !preg_match_all( self::ROUTE_REGEX, $path, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
            $path;
        }

        $regex = '';

        /**
         * Reference:
         *
         * $match[0]      - The entire match, ie: '[i:name]'
         * $match['name'] - The name of the placeholder
         * $match['type'] - The type of the placeholder
         */
        foreach ( $matches as $match ) {
            if ( empty( $match['name'] ) ) {
                $regex .= preg_quote( $match[0][0] );
                continue;
            }

            // Atomic groups were messing with names :(
            $regex .= '(';

            // Use appropriate regex
            switch ( $match['type'][0] ) {
                case 'i':
                    $regex .= '\d+';
                    break;

                case 's':
                default:
                    $regex .= '[a-zA-Z0-9-_]+';
                    break;
            }

            $regex .= ')';

            $route->args[] = $match['name'][0];
        }

        return $regex;
    }
}
