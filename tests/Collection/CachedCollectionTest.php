<?php

namespace Swiftly\Routing\Tests\Collection;

use Swiftly\Routing\Collection\CachedCollection;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class CachedCollectionTest Extends TestCase
{

    const COMPILED_REGEX = [
        'GET' => '~^(?|(?>/(*:home))|(?>/form/[s:name](*:form)))$~ixX',
        'POST' => '~^(?|(?>/post/[i:id](*:post))|(?>/form/[s:name](*:form)))$~ixX'
    ];

    /** @var CachedCollection $collection */
    private $collection;

    protected function setUp() : void
    {
        $this->collection = new CachedCollection( self::COMPILED_REGEX );
    }

    public function testReturnsCachedRegexForMethod() : void
    {
        foreach ( self::COMPILED_REGEX as $http_method => $expected ) {
            $regex = $this->collection->compile( $http_method );

            self::assertSame( $expected, $regex );
        }
    }
}
