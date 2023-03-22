<?php

namespace Swiftly\Routing\Parser;

use Swiftly\Routing\ParserInterface;
use Swiftly\Routing\Exception\UrlParseException;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Component\IntegerComponent;
use Swiftly\Routing\Component\StringComponent;
use Swiftly\Routing\Component\EnumComponent;

use function preg_match_all;
use function explode;
use function array_map;

use const PREG_SET_ORDER;
use const PREG_OFFSET_CAPTURE;

/**
 * Handles parsing the Swiftly URL syntax
 * 
 * @psalm-external-mutation-free
 */
class DefaultParser implements ParserInterface
{
    private const IDENTIFIER = '[A-Za-z_]+';
    private const INTEGER_COMPONENT = 'i';
    private const STRING_COMPONENT = 's';
    private const ENUM_COMPONENT = 'e';
    private const ENUM_VALUES = '<(?P<values>[A-Za-z_, ]+)>';

    private const REGEX = 
        '/(?>\['
            . '(?|'
                . '(?P<type>' . self::INTEGER_COMPONENT . ')|'
                . '(?P<type>' . self::STRING_COMPONENT . ')|'
                . '(?P<type>' . self::ENUM_COMPONENT . ')' . self::ENUM_VALUES
            . '):(?P<name>' . self::IDENTIFIER . ')'
        . '\])/';

    private const FLAGS = PREG_SET_ORDER|PREG_OFFSET_CAPTURE;

    public function parse(string $path): array
    {
        $result = preg_match_all(self::REGEX, $path, $matches, self::FLAGS);

        return [];
    }

    /**
     * Create a component of the given type
     * 
     * We can swap to using match when we get to PHP 8
     * 
     * @psalm-param 'i'|'s'|'e' $type
     * @psalm-param array{name:non-empty-string, values:string} $data
     * 
     * @param string $type   Component type
     * @param string[] $data Component data
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
            default:
                throw new Exception();
        }
    }
}
