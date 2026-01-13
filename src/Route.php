<?php declare(strict_types=1);

namespace Swiftly\Routing;

use OutOfBoundsException;
use Swiftly\Routing\ComponentInterface;

use function array_map;
use function count;
use function in_array;
use function is_string;
use function strtolower;
use function strtoupper;

/**
 * Represents a single endpoint in a HTTP aware application
 *
 * @psalm-immutable
 */
class Route
{
    /**
     * @psalm-var non-empty-list<string|ComponentInterface> $components
     * @var string[]|ComponentInterface[] $components
     */
    private array $components;

    /** @var callable $handler */
    private $handler;

    /** @psalm-var list<string> $methods */
    private array $methods;

    /** @psalm-var list<lowercase-string> $tags */
    private array $tags;

    /**
     * Create a new route from the given URL components
     *
     * @psalm-param non-empty-list<string|ComponentInterface> $components
     * @psalm-param list<string> $methods
     * @psalm-param list<string> $tags
     *
     * @param string[]|ComponentInterface[] $components URL components
     * @param callable $handler                         Controller for the route
     * @param string[] $methods                         Supported HTTP methods
     * @param string[] $tags                            Route tags
     */
    public function __construct(
        array $components,
        $handler,
        array $methods = [],
        array $tags = [],
    ) {
        $this->components = $components;
        $this->handler = $handler;
        $this->methods = array_map('strtoupper', $methods);
        $this->tags = array_map('strtolower', $tags);
    }

    /**
     * Return all the URL components for this route
     *
     * @psalm-return non-empty-list<string|ComponentInterface>
     *
     * @return string[]|ComponentInterface[]
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * Return the URL component at the given index
     *
     * @psalm-return ($index is 0 ? string : string|ComponentInterface)
     *
     * @throws OutOfBoundsException      If no component exists at the offset
     * @param int $index                 Component index
     * @return string|ComponentInterface
     */
    public function getComponent(int $index) // : string|ComponentInterface
    {
        if (!isset($this->components[$index])) {
            throw new OutOfBoundsException("No component exists at offset ($index)");
        }

        return $this->components[$index];
    }

    /**
     * Return the controller/handler attached to this route
     *
     * @return callable
     */
    public function getHandler() // : callable
    {
        return $this->handler;
    }

    /**
     * Return the HTTP methods supported by this route
     *
     * @psalm-return list<string>
     *
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Return all tags applied to this route
     *
     * @psalm-return list<lowercase-string>
     *
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Determine if this route is static or dynamic
     *
     * @psalm-assert-if-true array{0:string} $this->components
     * @psalm-assert-if-true array{0:string} $this->getComponents()
     *
     * @return bool
     */
    public function isStatic(): bool
    {
        return count($this->components) === 1
            && is_string($this->components[0]);
    }

    /**
     * Determine if this route supports the given HTTP method
     *
     * @psalm-param non-empty-string $method
     *
     * @param string $method HTTP method
     * @return bool
     */
    public function supports(string $method): bool
    {
        return empty($this->methods)
            || in_array(strtoupper($method), $this->methods, true);
    }

    /**
     * Determine if this route has the given tag
     *
     * @psalm-param non-empty-string $tag
     *
     * @param string $tag Route tag
     * @return bool
     */
    public function hasTag(string $tag): bool
    {
        return in_array(strtolower($tag), $this->tags, true);
    }
}
