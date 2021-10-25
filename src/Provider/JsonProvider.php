<?php

namespace Swiftly\Routing\Provider;

use Swiftly\Routing\ProviderInterface;
use Swiftly\Routing\Collection\RouteCollection;
use Swiftly\Routing\Route;

use function is_array;
use function is_readable;
use function file_get_contents;
use function json_decode;
use function json_last_error;
use function is_string;
use function is_callable;
use function rtrim;
use function explode;
use function array_filter;

use const JSON_ERROR_NONE;

/**
 * Class responsible for loading routes from .json files
 *
 * @psalm-type RDef=array{path: string, handler: callable}
 *
 * @author clvarley
 */
Class JsonProvider Implements ProviderInterface
{

    const HTTP_METHODS = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'UPDATE'
    ];

    /**
     * Path to the JSON route file
     *
     * @var string $filepath Absolute path
     */
    private $filepath;

    /**
     * Create a new provider from a JSON file
     *
     * @param string $filepath Absolute path
     */
    public function __construct( string $filepath )
    {
        $this->filepath = $filepath;
    }

    /**
     * {@inheritdoc}
     */
    public function populate( RouteCollection $collection ) : RouteCollection
    {
        // Can't progress, file invalid
        if ( !$this->readable() ) {
            return $collection;
        }

        $json = $this->getJson();

        foreach ( $json as $name => $contents ) {
            if ( !$this->valid( $contents ) ) {
                continue;
            }

            // Create route definition struct
            $route = $this->convert( $contents );

            $collection->set( (string)$name, $route );
        }

        return $collection;
    }

    /**
     * Is the JSON file actually readable
     *
     * @return bool Readable file
     */
    private function readable() : bool
    {
        return is_readable( $this->filepath );
    }

    /**
     * Attempts to get the decoded file contents
     *
     * @return array JSON data
     */
    private function getJson() : array
    {
        $content = (string)file_get_contents( $this->filepath );
        /** @var array|false $content */
        $content = json_decode( $content, true, 4 );

        // Parse error?
        if ( json_last_error() !== JSON_ERROR_NONE || !is_array( $content ) ) {
            $content = [];
        }

        return $content;
    }

    /**
     * Is this route definition valid
     *
     * @psalm-assert-if-true RDef $definition
     *
     * @param mixed $definition Route definition
     * @return bool             Valid definition
     */
    private function valid( $definition ) : bool
    {
        return ( is_array( $definition )
            && !empty( $definition['path'] )
            && !empty( $definition['handler'] )
            && is_string( $definition['path'] )
            && is_callable( $definition['handler'], true )
        );
    }

    /**
     * Converts the JSON array into a Route object
     *
     * @psalm-param RDef $definition
     *
     * @param array $definition Route definition
     * @return Route            Route object
     */
    private function convert( array $definition ) : Route
    {
        // Trim trailing chars
        $path = rtrim( $definition['path'], " \n\r\t\0\x0B\\/" );

        if ( empty( $path ) ) {
            $path = '/';
        }

        // Controller/handler function
        if ( is_string( $definition['handler'] ) ) {
            $handler = explode( '::', $definition['handler'] );
        } else {
            $handler = $definition['handler'];
        }

        $route = new Route( $path, $handler );

        // Allowed HTTP verbs only
        if ( !empty( $definition['methods'] ) && is_array( $definition['methods'] ) ) {
            $methods = array_intersect( $definition['methods'], self::HTTP_METHODS );
        } else {
            $methods = [ 'GET' ];
        }

        $route->methods = $methods;

        // Has tags?
        if ( !empty( $definition['tags'] ) && is_array( $definition['tags'] ) ) {
            $route->tags = array_filter( $definition['tags'], 'is_string' );
        }

        return $route;
    }
}
