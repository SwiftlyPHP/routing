<?php

namespace Swiftly\Routing\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Parser\DefaultParser;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Exception\ParseException;

Class DefaultParserTest Extends TestCase
{
    /** @var DefaultParser $parser */
    private $parser;

    public function setUp(): void
    {
        $this->parser = new DefaultParser();
    }

    public function testCanParseStaticUrl(): void
    {
        $components = $this->parser->parse('/admin');

        self::assertIsArray($components);
        self::assertCount(1, $components);
        self::assertContainsOnly('string', $components);
        self::assertContains('/admin', $components);
    }

    public function testCanParseDynamicUrl(): void
    {
        $components = $this->parser->parse('/post/[s:category]/[s:slug]');

        self::assertIsArray($components);
        self::assertCount(4, $components);

        // Return order *IS* important
        self::assertArrayHasKey(0, $components);
        self::assertSame('/post/', $components[0]);

        self::assertArrayHasKey(1, $components);
        self::assertInstanceOf(ComponentInterface::class, $components[1]);

        self::assertArrayHasKey(2, $components);
        self::assertSame('/', $components[2]);

        self::assertInstanceOf(3, $components);
        self::assertInstanceOf(ComponentInterface::class, $components[3]);
    }

    public function testThrowsOnInvalidUrl(): void
    {
        self::expectException(UrlParseException::class);

        $this->parser->parse('@#://invalid');
    }
}
