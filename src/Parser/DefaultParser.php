<?php declare(strict_types=1);

namespace Swiftly\Routing\Parser;

use Swiftly\Routing\Component\EnumComponent;
use Swiftly\Routing\Component\IntegerComponent;
use Swiftly\Routing\Component\StringComponent;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Exception\ComponentParseException;
use Swiftly\Routing\Exception\UrlParseException;
use Swiftly\Routing\ParserInterface;

use function array_map;
use function explode;
use function preg_match;
use function preg_match_all;

use const PREG_SET_ORDER;

/**
 * Handles parsing the Swiftly URL syntax
 *
 * @psalm-type ComponentMatch=array{type:'i'|'s'|'e',name:non-empty-string,values:string,...}
 * @psalm-type UrlMatch=array{0:string}
 * @psalm-external-mutation-free
 */
class DefaultParser implements ParserInterface
{
    private const ALLOWED_URL_CHARACTERS = '[a-zA-Z0-9\-_\.\~\/]+';
    private const IDENTIFIER = '[A-Za-z_]+';
    private const INTEGER_COMPONENT = 'i';
    private const STRING_COMPONENT = 's';
    private const ENUM_COMPONENT = 'e';
    private const ENUM_VALUES = '<(?P<values>[A-Za-z_, ]+)>';
    private const VALIDATION_REGEX = '/^\/(?>(?:[a-zA-Z0-9\-_\.\~\/]+|\[[a-zA-Z_:<>,]{3,}\])*)$/';
    private const SPLIT_REGEX =
        '/(?>'
        .   '(' . self::ALLOWED_URL_CHARACTERS . ')|'
        .   '(?:'
        .       '\['
        .       '(?|'
        .           '(?P<type>' . self::INTEGER_COMPONENT . ')|'
        .           '(?P<type>' . self::STRING_COMPONENT . ')|'
        .           '(?P<type>' . self::ENUM_COMPONENT . ')' . self::ENUM_VALUES
        .        '):(?P<name>' . self::IDENTIFIER . ')'
        .       '\]'
        .   ')'
        .   ')/';

    /** {@inheritDoc} */
    public function parse(string $path): array
    {
        if (!self::validate($path)) {
            throw new UrlParseException($path);
        }

        $parts = self::split($path);

        /** @var non-empty-list<ComponentMatch&UrlMatch> $parts */
        return self::convert($parts);
    }

    /**
     * Determine if the given path looks like a valid URL
     *
     * @param non-empty-string $path Subject path
     */
    private static function validate(string $path): bool
    {
        return preg_match(self::VALIDATION_REGEX, $path) === 1;
    }

    /**
     * Perform the regex used to split a path into a sequence of components
     *
     * @psalm-return list<ComponentMatch&UrlMatch>
     *
     * @param non-empty-string $path
     */
    private static function split(string $path): array
    {
        if (false === preg_match_all(self::SPLIT_REGEX, $path, $matches, PREG_SET_ORDER)) {
            throw new ComponentParseException($path);
        }

        /** @var list<ComponentMatch&UrlMatch> $matches */
        return $matches;
    }

    /**
     * Convert the regex matches into a components array
     *
     * @psalm-param non-empty-list<ComponentMatch&UrlMatch> $matches
     * @psalm-return non-empty-list<ComponentInterface|string>
     *
     * @param string[][] $matches            Regex matches
     * @return ComponentInterface[]|string[] Route components
     */
    private static function convert(array $matches): array
    {
        $components = [];

        foreach ($matches as $match) {
            $components[] = isset($match['type'])
                ? self::make($match['type'], $match)
                : $match[0];
        }

        return $components;
    }

    /**
     * Create a URL component using data from the route definition.
     *
     * @psalm-param ComponentMatch $data
     *
     * @param "i"|"s"|"e" $type   Component type
     * @param string[] $data      Component definition
     *
     * @return ComponentInterface Prepared component instance
     */
    private static function make(string $type, array $data): ComponentInterface
    {
        return match ($type) {
            self::INTEGER_COMPONENT => new IntegerComponent($data['name']),
            self::STRING_COMPONENT => new StringComponent($data['name']),
            self::ENUM_COMPONENT => new EnumComponent(
                $data['name'],
                array_map('trim', explode(',', $data['values'])),
            ),
        };
    }
}
