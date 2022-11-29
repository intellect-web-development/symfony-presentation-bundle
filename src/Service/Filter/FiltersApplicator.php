<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\Filter;

use Symfony\PresentationBundle\Dto\Input\Filter;
use Symfony\PresentationBundle\Dto\Input\Filters;

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
            $aliasPath = Helper::makeAliasPathFromPropertyPath("$fieldPrefix.{$filter->getProperty()}");
        } else {
            $aliasPath = "$fieldPrefix.{$filter->getProperty()}";
        }

        /** @var array|string|int|null $value */
        $value = $filter->getValue();

        switch ($filter->getSearchMode()) {
            case FilterSqlBuilder::NOT_IN:
                if (isset($value) && is_array($value)) {
                    $appSqlBuilder->notIn($aliasPath, $value);
                }
                break;
            case FilterSqlBuilder::IN:
                if (isset($value) && is_array($value)) {
                    $appSqlBuilder->in($aliasPath, $value);
                }
                break;
            case FilterSqlBuilder::RANGE:
                if (isset($value) && is_string($value)) {
                    self::rangeDecorator($appSqlBuilder, $value, $aliasPath);
                }
                break;
            case FilterSqlBuilder::IS_NULL:
                $appSqlBuilder->isNull($aliasPath);
                break;
            case FilterSqlBuilder::NOT_NULL:
                $appSqlBuilder->notNull($aliasPath);
                break;
            case FilterSqlBuilder::LESS_THAN:
            case FilterSqlBuilder::LESS_THAN_ALIAS_1:
            case FilterSqlBuilder::LESS_THAN_ALIAS_2:
                $appSqlBuilder->lessThan($aliasPath, $value);
                break;
            case FilterSqlBuilder::GREATER_THAN:
            case FilterSqlBuilder::GREATER_THAN_ALIAS_1:
            case FilterSqlBuilder::GREATER_THAN_ALIAS_2:
                $appSqlBuilder->greaterThan($aliasPath, $value);
                break;
            case FilterSqlBuilder::LESS_OR_EQUALS:
            case FilterSqlBuilder::LESS_OR_EQUALS_ALIAS_1:
            case FilterSqlBuilder::LESS_OR_EQUALS_ALIAS_2:
                $appSqlBuilder->lessOrEquals($aliasPath, $value);
                break;
            case FilterSqlBuilder::GREATER_OR_EQUALS:
            case FilterSqlBuilder::GREATER_OR_EQUALS_ALIAS_1:
            case FilterSqlBuilder::GREATER_OR_EQUALS_ALIAS_2:
                $appSqlBuilder->greaterOrEquals($aliasPath, $value);
                break;
            case FilterSqlBuilder::LIKE:
                $appSqlBuilder->like($aliasPath, $value);
                break;
            case FilterSqlBuilder::NOT_LIKE:
                $appSqlBuilder->notLike($aliasPath, $value);
                break;
            case FilterSqlBuilder::EQUALS:
            case FilterSqlBuilder::EQUALS_ALIAS_1:
            case FilterSqlBuilder::EQUALS_ALIAS_2:
                $appSqlBuilder->equals($aliasPath, $value);
                break;
            case FilterSqlBuilder::NOT_EQUALS:
            case FilterSqlBuilder::NOT_EQUALS_ALIAS_1:
            case FilterSqlBuilder::NOT_EQUALS_ALIAS_2:
            case FilterSqlBuilder::NOT_EQUALS_ALIAS_3:
                $appSqlBuilder->notEquals($aliasPath, $value);
                break;
        }
    }

    protected static function rangeDecorator(
        FilterSqlBuilder $appSqlBuilder,
        string $value,
        string $field
    ): FilterSqlBuilder {
        [$gte, $lte] = explode(',', $value);
        if (self::isDateTime($gte) && self::isDateTime($lte)) {
            return $appSqlBuilder->rangeDateTime($field, new \DateTime($gte), new \DateTime($lte));
        }

        return $appSqlBuilder->range($field, $gte, $lte);
    }

    private static function isDateTime(mixed $date): bool
    {
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d',
        ];

        foreach ($formats as $format) {
            $d = \DateTime::createFromFormat($format, $date);
            if ($d && $d->format($format) === $date) {
                return true;
            }
        }

        return false;
    }
}
