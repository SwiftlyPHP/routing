<?php declare(strict_types=1);

namespace Swiftly\Routing\Matcher;

use Swiftly\Routing\MatchedRoute;
use Swiftly\Routing\MatcherInterface;

/**
 * A matcher that forwards calls to other matchers in sequence
 */
class SeriesMatcher implements MatcherInterface
{
    /**
     * Wrap the given matchers, allowing them to be called one after the other
     *
     * @param MatcherInterface[] $matchers
     */
    public function __construct(
        private array $matchers,
    ) {
    }

    /**
     * {@inheritDoc}
     */
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
