<?php declare(strict_types=1);

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
     */
    public function __construct(string $filePath, string $expectedType)
    {
        parent::__construct(
            sprintf(
                "Failed to parse: '%s', are you sure it is valid %s?",
                $filePath,
                $expectedType,
            ),
        );
    }
}
