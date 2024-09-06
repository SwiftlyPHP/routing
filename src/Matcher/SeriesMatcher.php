<?php declare(strict_types=1);

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

    public function match(string $url, string $method = 'GET'): ?MatchedRoute
    {
        foreach ($this->matchers as $matcher) {
            if (null !== ($match = $matcher->match($url, $method))) {
                return $match;
            }
        }

        return null;
    }
}
