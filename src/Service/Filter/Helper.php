<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\Filter;

class Helper
{
    public static function makeAliasPathFromPropertyPath(string $propertyPath): string
    {
        $aliasParts = explode('.', $propertyPath);
        $property = array_pop($aliasParts);

        return implode('__', $aliasParts) . ".{$property}";
    }

    public static function makeRelationPath(array $explodePropertyPath, int $level): string
    {
        return implode('.', array_slice($explodePropertyPath, 0, $level));
    }

    public static function pathToAlias(string $path): string
    {
        return str_replace('.', '__', $path);
    }
}
