<?php

namespace Swiftly\Routing\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Parser\DefaultParser;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Component\StringComponent;
use Swiftly\Routing\Component\IntegerComponent;
use Swiftly\Routing\Component\EnumComponent;
use Swiftly\Routing\Exception\UrlParseException;
use Swiftly\Routing\Exception\ComponentParseException;

/**
 * @covers \Swiftly\Routing\Parser\DefaultParser
 * @covers \Swiftly\Routing\Exception\UrlParseException
 * @uses \Swiftly\Routing\Component\StringComponent
 * @uses \Swiftly\Routing\Component\IntegerComponent
 * @uses \Swiftly\Routing\Component\EnumComponent
 */
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

        self::assertArrayHasKey(3, $components);
        self::assertInstanceOf(ComponentInterface::class, $components[3]);
    }

    public function testCanParseStringComponent(): void
    {
        $components = $this->parser->parse('/[s:slug]');

        self::assertIsArray($components);
        self::assertCount(2, $components);

        self::assertArrayHasKey(0, $components);
        self::assertSame('/', $components[0]);

        self::assertArrayHasKey(1, $components);
        self::assertInstanceOf(StringComponent::class, $components[1]);
        self::assertSame('slug', $components[1]->name());
    }

    public function testCanParseIntegerComponent(): void
    {
        $components = $this->parser->parse('/[i:id]');

        self::assertIsArray($components);
        self::assertCount(2, $components);

        self::assertArrayHasKey(0, $components);
        self::assertSame('/', $components[0]);

        self::assertArrayHasKey(1, $components);
        self::assertInstanceOf(IntegerComponent::class, $components[1]);
        self::assertSame('id', $components[1]->name());
    }

    public function testCanParseEnumComponent(): void
    {
        $components = $this->parser->parse('/[e<uk,us>:country]');

        self::assertIsArray($components);
        self::assertCount(2, $components);

        self::assertArrayHasKey(0, $components);
        self::assertSame('/', $components[0]);

        self::assertArrayHasKey(1, $components);
        self::assertInstanceOf(EnumComponent::class, $components[1]);
        self::assertSame('country', $components[1]->name());
        self::assertTrue($components[1]->accepts('uk'));
        self::assertTrue($components[1]->accepts('us'));
    }

    public function testThrowsOnInvalidUrl(): void
    {
        self::expectException(UrlParseException::class);

        $this->parser->parse('@#://invalid');
    }

    // public function testThrowsOnInvalidComponent(): void
    // {
    //     self::expectException(ComponentParseException::class);

    //     // Note: Missing enum values
    //     $this->parser->parse('/posts/[e:post_type]');
    // }
}
