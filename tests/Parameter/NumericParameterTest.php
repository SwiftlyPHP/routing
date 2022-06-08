<?php

namespace Swiftly\Routing\Tests\Parameter;

use Swiftly\Routing\Parameter\NumericParameter;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class NumericParameterTest Extends TestCase
{

    public function testCanValidateNumber() : void
    {
        $parameter = new NumericParameter( 'numeric' );

        self::assertTrue( $parameter->validate( 123 ) );
        self::assertTrue( $parameter->validate( 001 ) );
        self::assertTrue( $parameter->validate( '123' ) );
        self::assertFalse( $parameter->validate( 'test' ) );
        self::assertFalse( $parameter->validate( 'nine' ) );
        self::assertFalse( $parameter->validate( [0] ) );
    }

    public function testCanCompileRegex() : void
    {
        $parameter = new NumericParameter( 'numeric' );

        self::assertSame( '(\d+)', $parameter->regex() );
        self::assertSame( '(\d+)', (string)$parameter );
    }

    public function testCanEscapeValue() : void
    {
        $parameter = new NumericParameter( 'numeric' );

        self::assertSame( '200', $parameter->escape( '200' ) );
        self::assertSame( '400', $parameter->escape( 400 ) );
    }
}
