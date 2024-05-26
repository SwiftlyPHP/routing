<?php

namespace Swiftly\Routing\Parser;

use Swiftly\Routing\ParserInterface;
use Swiftly\Routing\Exception\UrlParseException;
use Swiftly\Routing\Exception\ComponentParseException;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Component\IntegerComponent;
use Swiftly\Routing\Component\StringComponent;
use Swiftly\Routing\Component\EnumComponent;

use function preg_match;
use function preg_match_all;
use function explode;
use function array_map;

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
            .   '\['
            .   '(?|'
                .   '(?P<type>' . self::INTEGER_COMPONENT . ')|'
                .   '(?P<type>' . self::STRING_COMPONENT . ')|'
                .   '(?P<type>' . self::ENUM_COMPONENT . ')' . self::ENUM_VALUES
            .   '):(?P<name>' . self::IDENTIFIER . ')'
            .   '\]'
        .   ')'
    .   ')/';

    /** {@inheritDoc} */
    public function parse(string $path): array
    {
        // Does it look URL-like?
        if (!$this->validate($path)) {
            throw new UrlParseException($path);
        }

        // Did we strip any components?
        if (($parts = $this->split($path)) === null) {
            throw new ComponentParseException($path);
        }
        
        /** @var non-empty-list<ComponentMatch&UrlMatch> $parts */
        return $this->convert($parts);
    }

    /**
     * Determine if the given path looks like a valid URL
     * 
     * @psalm-param non-empty-string $path
     * 
     * @param string $path Subject path
     * @return bool        Is valid?
     */
    private function validate(string $path): bool
    {
        return preg_match(self::VALIDATION_REGEX, $path) === 1;
    }

    /**
     * Perform the regex used to split a path into a sequence of components
     * 
     * @psalm-param non-empty-string $path
     * @psalm-return list<ComponentMatch&UrlMatch>|null
     * 
     * @param string $path Subject path
     * @return array|null  Components parts
     */
    private function split(string $path): ?array
    {
        if (false === preg_match_all(self::SPLIT_REGEX, $path, $matches, PREG_SET_ORDER)) {
            return null;
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
    private function convert(array $matches): array
    {
        $components = [];

        foreach ($matches as $match) {
            $components[] = isset($match['type'])
                ? $this->make($match['type'], $match)
                : $match[0];
        }

        return $components;
    }

    /**
     * Create a component of the given type
     * 
     * We can swap to using match when we get to PHP 8
     * 
     * @php:8.0 Swap to using match expression
     * @psalm-param 'i'|'s'|'e' $type
     * @psalm-param ComponentMatch $data
     * 
     * @param string $type        Component type
     * @param string[] $data      Component data
     * @return ComponentInterface Prepared component instance
     */
    private function make(string $type, array $data): ComponentInterface
    {
        switch ($type) {
            case self::INTEGER_COMPONENT:
                return new IntegerComponent($data['name']);
            case self::STRING_COMPONENT:
                return new StringComponent($data['name']);
            case self::ENUM_COMPONENT:
                return new EnumComponent(
                    $data['name'],
                    array_map('trim', explode(',', $data['values']))
                );
        }
    }
}
