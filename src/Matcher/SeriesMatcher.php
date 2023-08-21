<?php

namespace Swiftly\Routing\Matcher;

use Swiftly\Routing\MatcherInterface;
use Swiftly\Routing\MatchedRoute;

/**
 * A matcher that forwards calls to other matchers in sequence
 */
class SeriesMatcher implements MatcherInterface
{
    /** @var MatcherInterface[] $matchers */
    private array $matchers;

    /**
     * Wrap the given matchers, allowing them to be called one after the other
     * 
     * @param MatcherInterface[] $matchers
     */
    public function __construct(array $matchers)
    {
        $this->matchers = $matchers;
    }

    public function match(string $url): ?MatchedRoute
    {
        foreach ($this->matchers as $matcher) {
            if (($match = $matcher->match($url)) !== null) return $match;
        }

        return null;
    }
}
