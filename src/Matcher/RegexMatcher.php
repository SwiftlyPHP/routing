<?php declare(strict_types=1);

namespace Swiftly\Routing\Matcher;

use Swiftly\Routing\Collection;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\MatchedRoute;
use Swiftly\Routing\MatcherInterface;
use Swiftly\Routing\Route;

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
    /** @var array<string, non-empty-string> */
    private array $compiled = [];

    /**
     * Create a new matcher for dynamic routes
     */
    public function __construct(
        private Collection $routes,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function match(string $url, string $method = 'GET'): ?MatchedRoute
    {
        $regex = $this->getRegex($method);

        if (!preg_match($regex, $url, $matches) || empty($matches['MARK'])) {
            return null;
        }

        $name = $matches['MARK'];
        $route = $this->routes->get($name);

        // TODO: Getting Psalm to understand matching is a nightmare, think of
        // longer term workaround.
        assert($route instanceof Route);

        // Index 0 of $matches is the full match, offset
        $index = 1;
        $args = [];

        foreach ($route->getComponents() as $component) {
            if ($component instanceof ComponentInterface === false) {
                continue;
            }

            $args[$component->name()] = $matches[$index++];
        }

        return new MatchedRoute($name, $route, $args);
    }

    /**
     * Returns the regex required for matching against routes.
     *
     * @param non-empty-string $method HTTP verb
     *
     * @return non-empty-string
     */
    private function getRegex(string $method): string
    {
        if (!isset($this->compiled[$method])) {
            $this->compiled[$method] = $this->compileRegex($method);
        }

        return $this->compiled[$method];
    }

    /**
     * Compiles the regex used for matching against routes.
     *
     * @param non-empty-string $method HTTP verb
     *
     * @return non-empty-string
     */
    private function compileRegex(string $method): string
    {
        $regexes = [];

        foreach ($this->routes->dynamic() as $name => $route) {
            if ($route->supports($method)) {
                $regexes[] = "(?>{$this->compileRoute($route)}(*:{$name}))";
            }
        }

        return '~^(?|' . implode('|', $regexes) . ')$~ixX';
    }

    /**
     * Creates the regex required for matching a single route.
     */
    private static function compileRoute(Route $route): string
    {
        $regex = '';

        foreach ($route->getComponents() as $component) {
            if ($component instanceof ComponentInterface) {
                $component = $component->regex();
            }

            $regex .= $component;
        }

        return $regex;
    }
}
