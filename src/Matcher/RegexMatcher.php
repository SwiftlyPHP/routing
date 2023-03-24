<?php

namespace Swiftly\Routing\Matcher;

use Swiftly\Routing\MatcherInterface;
use Swiftly\Routing\Collection;
use Swiftly\Routing\MatchedRoute;
use Swiftly\Routing\Route;
use Swiftly\Routing\ComponentInterface;

use function assert;
use function implode;
use function preg_match;

/**
 * Provides matching for dynamic routes
 * 
 * @psalm-external-mutation-free
 */
class RegexMatcher implements MatcherInterface
{
    /** @var Collection $routes Known routes */
    private $routes;

    /** @var string|null $compiled Compiled regex */
    private $compiled = null;

    /**
     * Create a new matcher for dynamic routes
     * 
     * @param Collection $routes Registered routes
     */
    public function __construct(Collection $routes)
    {
        $this->routes = $routes;
    }

    public function match(string $url): ?MatchedRoute
    {
        $regex = $this->getRegex();
            
        if (!preg_match($regex, $url, $matches) || empty($matches["MARK"])) {
            return null;
        }

        $name = $matches["MARK"];
        $route = $this->routes->get($name);

        // TODO: Getting Psalm to understand matching is a nightmare, think of
        // longer term workaround.
        assert($route instanceof Route);

        // Index 0 of $matches is the full match, offset
        $index = 1;
        $args = [];

        foreach ($route->getComponents() as $component) {
            if ($component instanceof ComponentInterface === false) continue;

            $args[$component->name()] = $matches[$index++];
        }

        return new MatchedRoute($name, $route, $args);
    }

    /**
     * Returns the regex required for matching against routes
     * 
     * @param string Regular expression
     */
    private function getRegex(): string
    {
        if ($this->compiled === null) {
            $this->compiled = $this->compileRegex();
        }

        return $this->compiled;
    }

    /**
     * Compiles the regex used for matching against routes
     * 
     * @return string Regular expression
     */
    private function compileRegex(): string
    {
        $regexes = [];

        foreach ($this->routes->dynamic() as $name => $route) {
            $regexes[] = "(?>{$this->compileRoute($route)}(*:{$name}))";
        }

        return '~^(?|' . implode('|', $regexes) . ')$~ixX';
    }

    /**
     * Creates the regex required for matching a single route
     * 
     * @return string Regular expression
     */
    private function compileRoute(Route $route): string
    {
        $regex = "";

        foreach ($route->getComponents() as $component) {
            if ($component instanceof ComponentInterface) {
                $component = $component->regex();
            }

            $regex .= $component;
        }

        return $regex;
    }
}
