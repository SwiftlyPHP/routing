<?php

namespace Swiftly\Routing\Exception;

use Exception;

use function sprintf;

/**
 * Exception to be thrown when a file read fails (for whatever reason)
 */
Class FileReadException Extends Exception
{
    /**
     * Indicate the given `$file_path` failed to load
     *
     * @param string $file_path Absolute file path
     */
    public function __construct(string $file_path)
    {
        parent::__construct(
            sprintf(
                'Failed to read: "%s", are you sure the file exists and is readable?',
                $file_path
            )
        );
    }
}
