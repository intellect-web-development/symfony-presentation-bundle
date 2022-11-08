<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\RequestParser\Implementations;

use Symfony\Component\HttpFoundation\Request;
use Symfony\PresentationBundle\Dto\Input\Filter;
use Symfony\PresentationBundle\Dto\Input\Filters;
use Symfony\PresentationBundle\Service\Filter\FilterSqlBuilder;
use Symfony\PresentationBundle\Service\RequestParser\Interfaces\FiltersMakerInterface;

class FiltersMaker implements FiltersMakerInterface
{
    public static function make(Request $request): Filters
    {
        /** @var mixed $filterRaw */
        $filterRaw = $request->query->all()['filter'];

        if (!isset($filterRaw)) {
            return new Filters();
        }
        if (!is_array($filterRaw)) {
            return new Filters();
        }

        $filters = [];
        /** @var int|string|null $property */
        /** @var array<string, mixed> $filterExpression */
        foreach ($filterRaw as $property => $filterExpression) {
            if (!self::propertyIsValid($property)) {
                continue;
            }
            if (!self::filterExpressionIsValid($filterExpression)) {
                continue;
            }

            /** @var mixed $value */
            $value = current($filterExpression);
            $mode = key($filterExpression);

            if (!self::valueIsValid($value)) {
                continue;
            }
            if (!self::modeIsValid($mode)) {
                continue;
            }
            /** @var string $mode */

            $filters[] = new Filter(
                (string) $property,
                $mode ?? FilterSqlBuilder::EQUALS,
                $value,
            );
        }

        return new Filters($filters);
    }

    private static function modeIsValid(?string $mode): bool
    {
        if (!isset($mode)) {
            return false;
        }
        if (!in_array($mode, FilterSqlBuilder::MODES)) {
            return false;
        }

        return true;
    }

    private static function valueIsValid(mixed $value): bool
    {
        if (!isset($value)) {
            return false;
        }
        if (!(is_string($value) || is_array($value))) {
            return false;
        }
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                if (!is_int($key)) {
                    return false;
                }
                if (!is_string($val)) {
                    return false;
                }
            }
        }

        return true;
    }

    private static function filterExpressionIsValid(mixed $filterExpression): bool
    {
        if (!isset($filterExpression)) {
            return false;
        }
        if (empty($filterExpression)) {
            return false;
        }
        if (!is_array($filterExpression)) {
            return false;
        }

        return true;
    }

    private static function propertyIsValid(mixed $property): bool
    {
        if (!isset($property)) {
            return false;
        }
        if (is_int($property)) {
            return false;
        }

        return true;
    }
}
