<?php

namespace Swiftly\Routing\Tests\Parameter;

use Swiftly\Routing\Parameter\EnumParameter;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class EnumParameterTest Extends TestCase
{

    public function testCanGetName() : void
    {
        $parameter = new EnumParameter( 'post', [ 'update', 'delete' ] );

        self::assertSame( 'post', $parameter->name() );
    }

    public function testCanValidateEnum() : void
    {
        $parameter = new EnumParameter( 'post', [ 'update', 'delete' ] );

        self::assertTrue( $parameter->validate( 'update' ) );
        self::assertTrue( $parameter->validate( 'delete' ) );
        self::assertFalse( $parameter->validate( 'create' ) );
        self::assertFalse( $parameter->validate( 'patch' ) );
    }

    public function testCanCompileRegex() : void
    {
        $parameter = new EnumParameter( 'post', [ 'update', 'delete' ] );

        $expected = '(update|delete)';

        self::assertSame( $expected, $parameter->regex() );
        self::assertSame( $expected, (string)$parameter );
    }

    public function testCanEscapeValue() : void
    {
        $parameter = new EnumParameter( 'status', [ '200', '404' ] );

        self::assertSame( '200', $parameter->escape( '200' ) );
        self::assertSame( '400', $parameter->escape( 400 ) );
    }
}
