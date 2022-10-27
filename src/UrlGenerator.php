<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Collection;
use Swiftly\Routing\Route;
use Swiftly\Routing\Exception\UndefinedRouteException;
use Swiftly\Routing\Exception\MissingArgumentException;
use Swiftly\Routing\Exception\FormatException;
use Swiftly\Routing\Exception\InvalidArgumentException;

use function is_string;

/**
 * Utility class used to generate URLs for named routes
 *
 * @psalm-immutable
 */
Final Class UrlGenerator
{
    /**
     * @var Collection $routes
     */
    private $routes;

    /**
     * Creates a new URL generator around the given routes
     *
     * @param Collection $routes Route collection
     */
    public function __construct(Collection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Generate a URL for the named route, optionally passing in any arguments
     *
     * @psalm-param non-empty-string $name
     * @psalm-param array<string,mixed> $args
     *
     * @throws UndefinedRouteException  If the named route doesn't exist
     * @throws MissingArgumentException If a required route argument is missing
     * @throws InvalidArgumentException If a given argument is invalid
     * @param string $name              Route name
     * @param mixed[] $args             Route arguments
     * @return string                   Generated URL
     */
    public function generate(string $name, array $args = []): string
    {
        $route = $this->routes->get($name);

        if ($route === null) {
            // TODO: Message for this exception
            throw new UndefinedRouteException();
        }

        $url = '';

        foreach ($route->getComponents() as $component) {
            if (is_string($component)) {
                $value = $component;
            } else {
                $value = $this->escape($component, $args);
            }

            $url .= $value;
        }

        return $url;
    }

    /**
     * Attempt to escape and format the value for a URL component
     *
     * @psalm-param array<string,mixed> $args
     *
     * @param ComponentInterface $component URL component
     * @param mixed[] $args                 Route arguments
     * @return string                       Escaped URL component
     */
    private function escape(ComponentInterface $component, array $args): string
    {
        $name = $component->name();

        if (!isset($args[$name])) {
            throw new MissingArgumentException($name);
        }

        try {
            $escaped = $component->escape($args[$name]);
        } catch (FormatException $exception) {
            // TODO: message for this exception
            throw new InvalidArgumentException('', 0, $exception);
        }

        return $escaped;
    }
}
