<?php

namespace Swiftly\Routing;

/**
 * Simple class used to represent a single route
 *
 * @author clvarley
 */
Class Route
{

    /**
     * The regex used to strip out URL args
     *
     * @var string ARGS_REGEX Regular expression
     */
    const ARGS_REGEX = '~\[(?:(?P<type>i|s):)?(?P<name>\w+)\]|(?:[^\[]+)~ix';

    /**
     * The name of this route
     *
     * @var string $name Route name
     */
    public $name = '';

    /**
     * The regex used to match this route
     *
     * @var string $regex Route regex
     */
    public $regex = '';

    /**
     * Allowed HTTP methods for this route
     *
     * @var string[] $methods HTTP methods
     */
    public $methods = [];

    /**
     * Arguments to be passed to the controller
     *
     * @var array $args Route arguments
     */
    public $args = [];

    /**
     * List of tags that apply to this route
     *
     * @var string[] $tags Route tags
     */
    public $tags = [];

    /**
     * The controller used to handle this route
     *
     * @var callable|null $callable Route controller
     */
    public $callable = null;

}
