<?php

namespace Swiftly\Routing\Exception;

use Exception;

use function sprintf;

/**
 * Exception to be thrown when a file does not parse from the given format
 */
class FileParseException extends Exception
{
    /**
     * Indicate the given `$file_path` did not parse
     *
     * @param string $file_path     Absolute file path
     * @param string $expected_type Expected file type
     */
    public function __construct(string $file_path, string $expected_type)
    {
        parent::__construct(
            sprintf(
                "Failed to parse: '%s', are you sure it is valid %s?",
                $file_path,
                $expected_type
            )
        );
    }
}
