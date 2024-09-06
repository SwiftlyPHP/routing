<?php declare(strict_types=1);

namespace Swiftly\Routing;

use Swiftly\Routing\Exception\FileReadException;
use Swiftly\Routing\Exception\FileParseException;

/**
 * Interface to signify classes capable of loading files from the filesystem
 */
interface FileLoaderInterface
{
    /**
     * Load this file, perform any required processing and return its content
     *
     * @throws FileReadException  If file cannot be read
     * @throws FileParseException If the file cannot be parsed
     * @return mixed[]
     */
    public function load(): array;
}
