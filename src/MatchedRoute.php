<?php declare(strict_types=1);

namespace Swiftly\Routing;

use Swiftly\Routing\Route;

/**
 * Plain old data structure used to store information about a matched route
 *
 * @internal
 *
 * @psalm-immutable
 */
class MatchedRoute
{
    /**
     * @readonly
     * @psalm-var non-empty-string $name
     * @var string $name
     */
    public string $name;

    /**
     * @readonly
     * @var Route $route
     */
    public Route $route;

    /**
     * @readonly
     * @psalm-var array<string,string> $args
     * @var string[] $args
     */
    public array $args;

    /**
     * Associate a route with the given name and arguments
     *
     * @psalm-param non-empty-string $name
     * @psalm-param array<string,string> $args
     *
     * @param string $name   Route name
     * @param Route $route   Matched route
     * @param string[] $args Matched arguments
     */
    public function __construct(string $name, Route $route, array $args = [])
    {
        $this->name = $name;
        $this->route = $route;
        $this->args = $args;
    }
}
