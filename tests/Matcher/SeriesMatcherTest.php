<?php declare(strict_types=1);

namespace Swiftly\Routing\Tests\Matcher;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\MatchedRoute;
use Swiftly\Routing\Matcher\SeriesMatcher;
use Swiftly\Routing\MatcherInterface;
use Swiftly\Routing\Route;

/**
 * @covers \Swiftly\Routing\Matcher\SeriesMatcher
 */
class SeriesMatcherTest extends TestCase
{
    private static $current;

    public function setUp(): void
    {
        self::$current = 0;
    }

    private static function expectCallOrder(int $order): callable
    {
        return function () use ($order) {
            self::assertSame(
                $order,
                self::$current++,
                'Matcher called in incorrect order!'
            );

            return null;
        };
    }

    public function testCanCallMatchersInSeries(): void
    {
        $matcher1 = $this->createMock(MatcherInterface::class);
        $matcher1->expects(self::once())
            ->method('match')
            ->with('/admin/users')
            ->willReturnCallback(self::expectCallOrder(0));

        $matcher2 = $this->createMock(MatcherInterface::class);
        $matcher2->expects(self::once())
            ->method('match')
            ->with('/admin/users')
            ->willReturnCallback(self::expectCallOrder(1));

        $series = new SeriesMatcher([
            $matcher1,
            $matcher2
        ]);

        self::assertNull($series->match('/admin/users'));
    }

    public function testCanGetFirstMatch(): void
    {
        $matcher1 = $this->createMock(MatcherInterface::class);
        $matcher1->expects(self::once())
            ->method('match')
            ->with('/admin/users')
            ->willReturn(
                new MatchedRoute('test', $this->createStub(Route::class), []),
            );

        $matcher2 = $this->createMock(MatcherInterface::class);
        $matcher2->expects(self::never())
            ->method('match');

        $series = new SeriesMatcher([
            $matcher1,
            $matcher2
        ]);

        $match = $series->match('/admin/users');

        self::assertInstanceOf(MatchedRoute::class, $match);
    }
}
