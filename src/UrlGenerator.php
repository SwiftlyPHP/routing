<?php declare(strict_types=1);

namespace Swiftly\Routing;

use Swiftly\Routing\Collection;
use Swiftly\Routing\Exception\FormatException;
use Swiftly\Routing\Exception\InvalidArgumentException;
use Swiftly\Routing\Exception\MissingArgumentException;
use Swiftly\Routing\Exception\UndefinedRouteException;
use Swiftly\Routing\UrlGeneratorInterface;

use function assert;

/**
 * Utility class used to generate URLs for named routes
 *
 * @psalm-immutable
 */
final class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * Creates a new URL generator around the given routes
     */
    public function __construct(
        private Collection $routes,
    ) {
    }

    /**
     * Generate a URL for the named route, optionally passing in any arguments
     *
     * @throws UndefinedRouteException  If the named route doesn't exist
     * @throws MissingArgumentException If a required route argument is missing
     * @throws InvalidArgumentException If a given argument is invalid
     *
     * @param non-empty-string $name     Route name
     * @param array<string, mixed> $args Route arguments
     *
     * @return non-empty-string
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

        assert('' !== $url);

        return $url;
    }

    /**
     * Attempt to escape and format the value for a URL component
     *
     * @psalm-pure
     *
     * @param array<string,mixed> $args Route arguments
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
