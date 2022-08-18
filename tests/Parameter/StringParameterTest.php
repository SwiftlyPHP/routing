<?php

namespace Swiftly\Routing\Tests\Parameter;

use Swiftly\Routing\Parameter\StringParameter;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class StringParameterTest Extends TestCase
{
    public function testCanGetName() : void
    {
        $parameter = new StringParameter( 'string' );

        self::assertSame( 'string', $parameter->name() );
    }

    public function testCanValidateString() : void
    {
        $parameter = new StringParameter( 'string' );

        self::assertTrue( $parameter->validate( 123 ) );
        self::assertTrue( $parameter->validate( 001 ) );
        self::assertTrue( $parameter->validate( '123' ) );
        self::assertTrue( $parameter->validate( 'test' ) );
        self::assertTrue( $parameter->validate( 'nine' ) );
        self::assertTrue(
            $parameter->validate(
                new class {
                    public function __toString() {}
                }
            )
        );
        self::assertFalse( $parameter->validate( [0] ) );
        self::assertFalse( $parameter->validate( new \stdClass ) );
    }

    public function testCanCompileRegex() : void
    {
        $parameter = new StringParameter( 'string' );

        self::assertSame( '([a-zA-Z0-9-_]+)', $parameter->regex() );
        self::assertSame( '([a-zA-Z0-9-_]+)', (string)$parameter );
    }

    public function testCanEscapeValue() : void
    {
        $parameter = new StringParameter( 'string' );

        self::assertSame( '200', $parameter->escape( '200' ) );
        self::assertSame( '400', $parameter->escape( 400 ) );
        self::assertSame( 'test',
            $parameter->escape(
                new class {
                    public function __toString() { return 'test'; }
                }
            )
        );
    }
}
