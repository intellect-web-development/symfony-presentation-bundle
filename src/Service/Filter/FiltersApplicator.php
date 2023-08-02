<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\Filter;

use DateTime;
use IWD\Symfony\PresentationBundle\Dto\Input\Filter;
use IWD\Symfony\PresentationBundle\Dto\Input\Filters;

class FiltersApplicator
{
    public static function applyMany(
        Filters $filters,
        FilterSqlBuilder $appSqlBuilder,
        string $fieldPrefix,
        bool $isRelation
    ): void {
        foreach ($filters->toArray() as $filter) {
            self::apply($filter, $appSqlBuilder, $fieldPrefix, $isRelation);
        }
    }

    public static function apply(
        Filter $filter,
        FilterSqlBuilder $appSqlBuilder,
        string $fieldPrefix,
        bool $isRelation
    ): void {
        if ($isRelation) {
            $aliasPath = Helper::makeAliasPathFromPropertyPath("$fieldPrefix.$filter->property");
        } else {
            $aliasPath = "$fieldPrefix.$filter->property";
        }

        /** @var array|string|int|null $value */
        $value = $filter->value;

        switch ($filter->mode) {
            case FilterMode::NotIn:
                if (isset($value) && is_array($value)) {
                    $appSqlBuilder->notIn($aliasPath, $value, $filter->strategy);
                }
                break;
            case FilterMode::In:
                if (isset($value) && is_array($value)) {
                    $appSqlBuilder->in($aliasPath, $value, $filter->strategy);
                }
                break;
            case FilterMode::Range:
                if (isset($value) && is_string($value)) {
                    self::rangeDecorator($appSqlBuilder, $value, $aliasPath, $filter->strategy);
                }
                break;
            case FilterMode::IsNull:
                $appSqlBuilder->isNull($aliasPath, $filter->strategy);
                break;
            case FilterMode::NotNull:
                $appSqlBuilder->notNull($aliasPath, $filter->strategy);
                break;
            case FilterMode::LessThan:
            case FilterMode::LessThanAlias1:
            case FilterMode::LessThanAlias2:
                $appSqlBuilder->lessThan($aliasPath, $value, $filter->strategy);
                break;
            case FilterMode::GreaterThan:
            case FilterMode::GreaterThanAlias1:
            case FilterMode::GreaterThanAlias2:
                $appSqlBuilder->greaterThan($aliasPath, $value, $filter->strategy);
                break;
            case FilterMode::LessOrEquals:
            case FilterMode::LessOrEqualsAlias1:
            case FilterMode::LessOrEqualsAlias2:
                $appSqlBuilder->lessOrEquals($aliasPath, $value, $filter->strategy);
                break;
            case FilterMode::GreaterOrEquals:
            case FilterMode::GreaterOrEqualsAlias1:
            case FilterMode::GreaterOrEqualsAlias2:
                $appSqlBuilder->greaterOrEquals($aliasPath, $value, $filter->strategy);
                break;
            case FilterMode::Like:
                $appSqlBuilder->like($aliasPath, $value, $filter->strategy);
                break;
            case FilterMode::NotLike:
                $appSqlBuilder->notLike($aliasPath, $value, $filter->strategy);
                break;
            case FilterMode::Equals:
            case FilterMode::EqualsAlias1:
            case FilterMode::EqualsAlias2:
                $appSqlBuilder->equals($aliasPath, $value, $filter->strategy);
                break;
            case FilterMode::NotEquals:
            case FilterMode::NotEqualsAlias1:
            case FilterMode::NotEqualsAlias2:
            case FilterMode::NotEqualsAlias3:
                $appSqlBuilder->notEquals($aliasPath, $value, $filter->strategy);
                break;
        }
    }

    protected static function rangeDecorator(
        FilterSqlBuilder $appSqlBuilder,
        string $value,
        string $field,
        FilterStrategy $filterStrategy,
    ): FilterSqlBuilder {
        [$gte, $lte] = explode(',', $value);
        if (self::isDateTime($gte) && self::isDateTime($lte)) {
            return $appSqlBuilder->rangeDateTime($field, new DateTime($gte), new DateTime($lte), $filterStrategy);
        }

        return $appSqlBuilder->range($field, $gte, $lte, $filterStrategy);
    }

    private static function isDateTime(mixed $date): bool
    {
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d',
        ];

        foreach ($formats as $format) {
            $d = DateTime::createFromFormat($format, $date);
            if ($d && $d->format($format) === $date) {
                return true;
            }
        }

        return false;
    }
}
