<?php declare(strict_types=1);

namespace Swiftly\Routing\File;

use Swiftly\Routing\FileLoaderInterface;
use Swiftly\Routing\Exception\FileReadException;
use Swiftly\Routing\Exception\FileParseException;

use function json_decode;
use function is_array;
use function is_file;
use function file_get_contents;
use function preg_match;

/**
 * Utility class used to load and parse JSON files
 */
class JsonFile implements FileLoaderInterface
{
    /** @var string $file_path */
    private string $file_path;

    /**
     * Wraps the given `$file_path` to be loaded and parsed as JSON
     *
     * @param string $file_path Absolute file path
     */
    public function __construct(string $file_path)
    {
        $this->file_path = $file_path;
    }

    /**
     * Attempts to load the file, parse it and then return its contents
     *
     * @throws FileReadException  If file cannot be read
     * @throws FileParseException If file is not valid JSON
     * @return mixed[]            Parsed values
     */
    public function load(): array
    {
        $content = $this->tryContent();

        if ($content === null) {
            throw new FileReadException($this->file_path);
        }

        // @php:8.3 Swap to json_validate when we can
        if (!self::isJsonLike($content)) {
            throw new FileParseException($this->file_path, 'json');
        }

        /** @var array|null @json */
        $json = json_decode($content, true);
        $json = is_array($json) ? $json : [];

        return $json;
    }

    /**
     * Try to load the file in it's textual form
     *
     * @return string|null
     */
    private function tryContent(): ?string
    {
        if (!is_file($this->file_path)) {
            return null;
        }

        $content = file_get_contents($this->file_path);
        $content = $content !== false ? $content : null;

        return $content;
    }

    /**
     * Determine if the given string looks JSON-like
     *
     * Can swap to using `json_validate` when support reaches PHP 8.3
     *
     * @param string $content Subject string
     * @return bool           Content is JSON
     */
    private function isJsonLike(string $content): bool
    {
        return preg_match("/^\s*(?:{\s*\")|(?:\[\s*{)/", $content) === 1;
    }
}
