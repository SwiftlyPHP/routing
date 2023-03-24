<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Route;

/**
 * Plain old data structure used to store information about a matched route
 *
 * @internal
 *
 * @psalm-immutable
 */
Class MatchedRoute
{
    /**
     * @readonly
     * @psalm-var non-empty-string $name
     * @var string $name
     */
    public $name;

    /**
     * @readonly
     * @var Route $route
     */
    public $route;

    /**
     * @readonly
     * @psalm-var array<string,string> $args
     * @var string[] $args
     */
    public $args;

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
