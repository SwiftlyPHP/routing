<?php declare(strict_types=1);

namespace Swiftly\Routing;

use Swiftly\Routing\Collection;
use Swiftly\Routing\Exception\FormatException;
use Swiftly\Routing\Exception\InvalidArgumentException;
use Swiftly\Routing\Exception\MissingArgumentException;
use Swiftly\Routing\Exception\UndefinedRouteException;
use Swiftly\Routing\UrlGeneratorInterface;

/**
 * Utility class used to generate URLs for named routes
 *
 * @psalm-immutable
 */
final class UrlGenerator implements UrlGeneratorInterface
{
    private Collection $routes;

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
     *
     * @param string $name              Route name
     * @param mixed[] $args             Route arguments
     * @return string                   Generated URL
     */
    public function generate(string $name, array $args = []): string
    {
        $url = '';

        foreach ($this->getRoute($name)->getComponents() as $component) {
            if ($component instanceof ComponentInterface) {
                $component = self::escape($component, $args);
            }

            $url .= $component;
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
    private static function escape(
        ComponentInterface $component,
        array $args,
    ): string {
        $name = $component->name();

        if (!isset($args[$name])) {
            throw new MissingArgumentException($name);
        }

        try {
            $escaped = $component->escape($args[$name]);
        } catch (FormatException $exception) {
            throw new InvalidArgumentException($name, $args[$name]);
        }

        return $escaped;
    }

    /**
     * @throws UndefinedRouteException
     *
     * @param non-empty-string $name
     */
    private function getRoute(string $name): Route
    {
        $route = $this->routes->get($name);

        if (null === $route) {
            throw new UndefinedRouteException($name);
        }

        return $route;
    }
}
