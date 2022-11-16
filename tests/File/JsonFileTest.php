<?php

namespace Swiftly\Routing\Tests\File;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\File\JsonFile;
use Swiftly\Routing\Exception\FileReadException;

use function file_put_contents;
use function json_encode;
use function unlink;

Class JsonFileTest Extends TestCase
{
    /** @var JsonFile $file */
    private $file;

    private const EXAMPLE_CONTENT = [
        'view' => [
            'path'    => '/view',
            'handler' => null,
            'methods' => ['GET'],
            'tags'    => ['cacheable']
        ],
        'edit' => [
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

    private const TEMP_FILE = __DIR__ . '/temp.json';

    public function setUp(): void
    {
        $this->file = new JsonFile(self::TEMP_FILE);
        file_put_contents(self::TEMP_FILE, json_encode(self::EXAMPLE_CONTENT));
    }

    public function tearDown(): void
    {
        unlink(self::TEMP_FILE);
    }

    public function testCanGetFileContents(): void
    {
        $contents = $this->file->load();

        self::assertIsArray($contents);
        self::assertCount(3, $contents);
        self::assertArrayHasKey('view', $contents);
        self::assertArrayHasKey('edit', $contents);
        self::assertArrayHasKey('delete', $contents);
        self::assertSame(self::EXAMPLE_CONTENT, $contents);
    }

    public function testThrowsOnUnreadableFile(): void
    {
        $file = new JsonFile('some_unreadable.json');

        self::expectException(FileReadException::class);

        $file->load();
    }
}
