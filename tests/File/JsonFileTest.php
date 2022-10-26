<?php

namespace Swiftly\Routing\Tests\File;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\File\JsonFile;
use Swiftly\Routing\Exception\ParseExcepton;

use function file_put_contents;
use function json_encode;

Class JsonFileTest Extends TestCase
{
    /** @var JsonFile $file */
    private $file;

    private const EXAMPLE_CONTENT = [
        'view'   => [
            'path'    => '/view',
            'handler' => null,
            'methods' => ['GET'],
            'tags'    => ['cacheable']
        ],
        'edit'   => [
            'path'    => '/edit',
            'handler' => null,
            'methods' => ['GET', 'POST'],
            'tags'    => []
        ],
        'delete' => [
            'path'    => '/delete',
            'handler' => null,
            'methods' => ['POST'],
            'tags'    => ['admin']
        ]
    ];

    public function setUp(): void
    {
        $this->file = new JsonFile('php://memory');
    }

    public static function setUpBeforeClass(): void
    {
        file_put_contents('php://memory', json_encode(self::EXAMPLE_CONTENT));
    }

    public static function tearDownAfterClass(): void
    {
        file_put_contents('php://memory', '');
    }

    public function testCanGetFileContents(): void
    {
        $contents = $this->file->contents();

        self::assertIsArray($contents);
        self::assertCount(3, $contents);
        self::assertArrayHasKey('view', $contents);
        self::assertArrayHasKey('edit', $contents);
        self::assertArrayHasKey('delete', $contents);
        self::assertSame(self::EXAMPLE_CONTENT, $contents);
    }

    public function testThrowsOnMalformedJson(): void
    {
        file_put_contents('php://memory', '<?not_json>');

        self::expectException(ParseExcepton::class);

        $this->file->contents();
    }
}
