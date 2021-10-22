<?php

namespace Swiftly\Routing\Parser;

use Swiftly\Routing\Collection\RouteCollection;
use Swiftly\Routing\CollectionInterface;
use Swiftly\Routing\ParserInterface;
use Swiftly\Routing\Route;

use function is_readable;
use function file_get_contents;
use function json_decode;
use function json_last_error;
use function is_array;
use function rtrim;
use function array_intersect;
use function array_filter;
use function is_string;
use function explode;

use const JSON_ERROR_NONE;

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
            
            $routes->add( (string)$name, $route );
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

        // Trim trailing chars
        $path = rtrim( $json['path'], " \n\r\t\0\x0B\\/" );

        if ( !empty( $path ) ) {
            $route->raw = $path;
        } else {
            $route->raw = '/';
        }

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
}
