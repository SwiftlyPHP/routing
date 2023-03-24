<?php

namespace Swiftly\Routing\Provider;

use Swiftly\Routing\ProviderInterface;
use Swiftly\Routing\FileLoaderInterface;
use Swiftly\Routing\ParserInterface;
use Swiftly\Routing\Collection;
use Swiftly\Routing\Route;
use Swiftly\Routing\Exception\RouteParseException;

use function is_string;
use function is_array;
use function strpos;
use function explode;
use function function_exists;
use function array_values;
use function array_filter;

class FileProvider implements ProviderInterface
{
    /** @var FileLoaderInterface $loader */
    private $loader;

    /** @var ParserInterface $parser */
    private $parser;

    /**
     * Create a provider around the given file, using the given parsing method
     */
    public function __construct(
        FileLoaderInterface $loader,
        ParserInterface $parser
    ) {
        $this->loader = $loader;
        $this->parser = $parser;
    }

    public function provide(): Collection
    {
        $routes = [];

        /** @var mixed $block */
        foreach ($this->loader->load() as $name => $block) {
            if (!is_string($name) || empty($name)) {
                throw new RouteParseException(
                    "Could not add unnamed route '$name'"
                );
            }

            if (!is_array($block)) {
                throw new RouteParseException(
                    "Could not add route '$name' as it has no definition"
                );
            }

            $routes[$name] = $this->tryParseBlock($name, $block);
        }

        return new Collection($routes);
    }

    /**
     * Attempt to parse a route definition block into a Route object
     * 
     * @param string $name Route name
     * @param array $block Route definition block
     * @return Route       Parsed route
     */
    private function tryParseBlock(string $name, array $block): Route
    {
        if (!isset($block['path'])
            || !is_string($block['path'])
            || empty($block['path'])
        ) {
            throw new RouteParseException(
                "Route '$name' is missing required 'path' attribute or it is a valid value"
            );
        }

        // To allow the "Controller::method" syntax, we need to do this here
        if (isset($block['handler'])
            && is_string($block['handler'])
            && strpos($block['handler'], '::', 1) !== false
        ) {
            $block['handler'] = explode('::', $block['handler'], 2);
        }

        if (!isset($block['handler']) || !self::isCallable($block['handler'])) {
            throw new RouteParseException(
                "Route '$name' is missing required 'handler' attribute or it is not a valid callable function"
            );
        }

        $components = $this->parser->parse($block['path']);
        $handler = $block['handler'];
        $methods = self::stripMethods($block);
        $tags = self::stripTags($block);

        return new Route($components, $handler, $methods, $tags);
    }

    /**
     * Attempt to determine if the given value is a callable
     * 
     * @psalm-assert-if-true callable $subject
     * 
     * @param mixed $subject
     * @return bool
     */
    private static function isCallable($subject): bool
    {
        if (is_string($subject) && function_exists($subject)) {
            return true;
        }

        if (is_array($subject) && self::isCallableArray($subject)) {
            return true;
        }
        
        return false;
    }

    /**
     * Attempt to determine if the given array is a callable
     * 
     * @psalm-assert-if-true callable $subject
     * @psalm-assert-if-true array{0:class-string,1:string} $subject
     * 
     * @param array $subject
     * @return bool
     */
    private static function isCallableArray(array $subject): bool
    {
        return (isset($subject[0], $subject[1]) 
            && is_string($subject[0])
            && is_string($subject[1])
        );
    }

    /**
     * Attempt to strip the HTTP methods from a route definition
     * 
     * @psalm-return non-empty-list<string>
     * 
     * @param array $block Route definition
     * @return string[]    HTTP methods
     */
    private static function stripMethods(array $block): array
    {
        if (!isset($block['methods']) || !is_array($block['methods'])) {
            return ['GET'];
        }

        $filtered = array_values(array_filter($block['methods'], 'is_string'));

        return $filtered ?: ['GET'];
    }

    /**
     * Attempt to strip the tags from a route definition
     * 
     * @psalm-return list<string>
     * 
     * @param array $block Route definition
     * @return string[]    Route tags
     */
    private static function stripTags(array $block): array
    {
        if (!isset($block['tags']) || !is_array($block['tags'])) {
            return [];
        }

        return array_values(array_filter($block['tags'], 'is_string'));
    }
}
