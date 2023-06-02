<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\RequestParser\Implementations;

use IWD\Symfony\PresentationBundle\Service\Filter\FilterMode;
use Symfony\Component\HttpFoundation\Request;
use IWD\Symfony\PresentationBundle\Dto\Input\Filter;
use IWD\Symfony\PresentationBundle\Dto\Input\Filters;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\FiltersMakerInterface;

class FiltersMaker implements FiltersMakerInterface
{
    public static function make(Request $request): Filters
    {
        $all = $request->query->all();
        $filterRaw = $all['filter'] ?? null;

        if (!isset($filterRaw) || !is_array($filterRaw)) {
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

            if (!self::valueIsValid($value)) {
                continue;
            }
            $filterMode = FilterMode::tryFrom(mb_strtolower((string) key($filterExpression))) ?? FilterMode::Equals;
            $filters[] = new Filter(
                (string) $property,
                $filterMode,
                $value,
            );
        }

        return new Filters($filters);
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
