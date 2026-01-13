<?php declare(strict_types=1);

namespace Swiftly\Routing\File;

use Swiftly\Routing\Exception\FileParseException;
use Swiftly\Routing\Exception\FileReadException;
use Swiftly\Routing\FileLoaderInterface;

use function file_get_contents;
use function is_array;
use function is_file;
use function json_decode;
use function preg_match;

/**
 * Utility class used to load and parse JSON files
 */
class JsonFile implements FileLoaderInterface
{
    /**
     * Wraps the given `$filePath` to be loaded and parsed as JSON
     */
    public function __construct(private string $filePath)
    {
    }

    /**
     * Attempts to load the file, parse it and then return its contents
     *
     * @throws FileReadException  If file cannot be read or isn't valid JSON
     *
     * @return mixed[]
     */
    public function load(): array
    {
        $content = $this->tryContent();

        if ($content === null) {
            throw new FileReadException($this->filePath);
        }

        // @upgrade:php8.3 Swap to json_validate
        if (!self::isJsonLike($content)) {
            throw new FileParseException($this->filePath, 'json');
        }

        /** @var array|null @json */
        $json = json_decode($content, true);
        $json = is_array($json) ? $json : [];

        return $json;
    }

    /**
     * Try to load the file in it's textual form
     */
    private function tryContent(): ?string
    {
        if (!is_file($this->filePath)) {
            return null;
        }

        $content = file_get_contents($this->filePath);
        $content = false !== $content ? $content : null;

        return $content;
    }

    /**
     * Determine if the given string looks JSON-like
     */
    private static function isJsonLike(string $content): bool
    {
        return preg_match("/^\s*(?:{\s*\")|(?:\[\s*{)/", $content) === 1;
    }
}
