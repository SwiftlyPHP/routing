<?php

namespace Swiftly\Routing\File;

use Swiftly\Routing\FileInterface;
use Swiftly\Routing\Exception\FileReadException;

use function json_decode;
use function is_array;
use function is_file;
use function file_get_contents;

/**
 * Utility class used to load and parse JSON files
 */
Class JsonFile Implements FileInterface
{
    /**
     * @var string $file_path
     */
    private $file_path;

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
     * @throws FileReadException If file cannot be read
     * @return mixed[]
     */
    public function load(): array
    {
        $content = $this->tryContent();

        if ($content === null) {
            // TODO: Update exception messaging
            throw new FileReadException();
        }

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
}
