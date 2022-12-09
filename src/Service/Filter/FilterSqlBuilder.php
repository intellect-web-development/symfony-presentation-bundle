<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\Filter;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use IWD\Symfony\PresentationBundle\Dto\Input\Pagination;
use IWD\Symfony\PresentationBundle\Dto\Input\Sort;
use IWD\Symfony\PresentationBundle\Dto\Input\Sorts;

class FilterSqlBuilder
{
    public const IN = 'in';
    public const LIKE = 'like';
    public const RANGE = 'range';
    public const NOT_IN = 'not-in';
    public const EQUALS = 'equals';
    public const EQUALS_ALIAS_1 = 'eq';
    public const EQUALS_ALIAS_2 = '=';
    public const IS_NULL = 'is-null';
    public const NOT_LIKE = 'not-like';
    public const NOT_NULL = 'not-null';
    public const LESS_THAN = 'less-than';
    public const LESS_THAN_ALIAS_1 = '<';
    public const LESS_THAN_ALIAS_2 = 'lt';
    public const NOT_EQUALS = 'not-equals';
    public const NOT_EQUALS_ALIAS_1 = '!=';
    public const NOT_EQUALS_ALIAS_2 = '<>';
    public const NOT_EQUALS_ALIAS_3 = 'neq';
    public const GREATER_THAN = 'greater-than';
    public const GREATER_THAN_ALIAS_1 = '>';
    public const GREATER_THAN_ALIAS_2 = 'gt';
    public const LESS_OR_EQUALS = 'less-or-equals';
    public const LESS_OR_EQUALS_ALIAS_1 = '<=';
    public const LESS_OR_EQUALS_ALIAS_2 = 'lte';
    public const GREATER_OR_EQUALS = 'greater-or-equals';
    public const GREATER_OR_EQUALS_ALIAS_1 = '>=';
    public const GREATER_OR_EQUALS_ALIAS_2 = 'gte';

    public const MODES = [
        self::NOT_IN,
        self::IN,
        self::RANGE,
        self::IS_NULL,
        self::NOT_NULL,
        self::LESS_THAN,
        self::LESS_THAN_ALIAS_1,
        self::LESS_THAN_ALIAS_2,
        self::GREATER_THAN,
        self::GREATER_THAN_ALIAS_1,
        self::GREATER_THAN_ALIAS_2,
        self::LESS_OR_EQUALS,
        self::LESS_OR_EQUALS_ALIAS_1,
        self::LESS_OR_EQUALS_ALIAS_2,
        self::GREATER_OR_EQUALS,
        self::GREATER_OR_EQUALS_ALIAS_1,
        self::GREATER_OR_EQUALS_ALIAS_2,
        self::LIKE,
        self::NOT_LIKE,
        self::EQUALS,
        self::EQUALS_ALIAS_1,
        self::EQUALS_ALIAS_2,
        self::NOT_EQUALS,
        self::NOT_EQUALS_ALIAS_1,
        self::NOT_EQUALS_ALIAS_2,
        self::NOT_EQUALS_ALIAS_3,
    ];

    public QueryBuilder $queryBuilder;
    private string $alias;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $aliases = $this->queryBuilder->getAllAliases();
        if (count($aliases) !== 0) {
            $this->alias = current($aliases);
        } else {
            $this->alias = 'entity';
        }
    }

    public function addSorts(Sorts $sorts): self
    {
        foreach ($sorts->toArray() as $sort) {
            $this->addSort($sort);
        }

        return $this;
    }

    public function addSort(?Sort $sort): self
    {
        if ($sort) {
            $this->queryBuilder->addOrderBy("{$this->alias}.{$sort->getField()}", $sort->getDirection());
        }

        return $this;
    }

    public function setPagination(Pagination $pagination): self
    {
        $this->queryBuilder
            ->setFirstResult($pagination->getOffset())
            ->setMaxResults($pagination->getPageSize())
        ;

        return $this;
    }

    public function equals(string $field, mixed $value): self
    {
        if (null !== $value) {
            $value = is_bool($value) ? (int) $value : $value;
            $bind = $this->bind($value);
            $this->queryBuilder->andWhere("{$field} = :{$bind}");
        }

        return $this;
    }

    public function notEquals(string $field, mixed $value): self
    {
        if (null !== $value) {
            $value = is_bool($value) ? (int) $value : $value;
            $bind = $this->bind($value);
            $this->queryBuilder->andWhere("{$field} != :{$bind}");
        }

        return $this;
    }

    public function like(string $field, mixed $value): self
    {
        if (!empty($value)) {
            $bind = $this->bind("%{$value}%");
            $this->queryBuilder->andWhere("{$field} LIKE :{$bind}");
        }

        return $this;
    }

    public function isNull(string $field): self
    {
        $this->queryBuilder->andWhere("{$field} IS NULL");

        return $this;
    }

    public function notNull(string $field): self
    {
        $this->queryBuilder->andWhere("{$field} IS NOT NULL");

        return $this;
    }

    public function notLike(string $field, mixed $value): self
    {
        if (!empty($value)) {
            $bind = $this->bind("%{$value}%");
            $this->queryBuilder->andWhere("{$field} NOT LIKE :{$bind}");
        }

        return $this;
    }

    public function in(string $field, ?array $values): self
    {
        if (!empty($values)) {
            $bind = $this->bind($values);
            $this->queryBuilder->andWhere(sprintf('%s IN (:%s)', $field, $bind));
        }

        return $this;
    }

    public function notIn(string $field, ?array $values): self
    {
        if (!empty($values)) {
            $bind = $this->bind($values);
            $this->queryBuilder->andWhere("{$field} NOT IN (:{$bind})");
        }

        return $this;
    }

    public function lessThan(string $field, mixed $lte): self
    {
        if (null !== $lte) {
            $lteBind = $this->bind($lte);
            $this->queryBuilder->andWhere("{$field} < :{$lteBind}");
        }

        return $this;
    }

    public function greaterThan(string $field, mixed $gte): self
    {
        if (null !== $gte) {
            $gteBind = $this->bind($gte);
            $this->queryBuilder->andWhere("{$field} > :{$gteBind}");
        }

        return $this;
    }

    public function lessOrEquals(string $field, mixed $lte): self
    {
        if (null !== $lte) {
            $lteBind = $this->bind($lte);
            $this->queryBuilder->andWhere("{$field} <= :{$lteBind}");
        }

        return $this;
    }

    public function greaterOrEquals(string $field, mixed $gte): self
    {
        if (null !== $gte) {
            $gteBind = $this->bind($gte);
            $this->queryBuilder->andWhere("{$field} >= :{$gteBind}");
        }

        return $this;
    }

    public function range(string $field, mixed $gte, mixed $lte): self
    {
        if (null !== $gte && null !== $lte) {
            $gteBind = $this->bind($gte);
            $lteBind = $this->bind($lte);
            $this->queryBuilder->andWhere("{$field} BETWEEN :{$gteBind} AND :{$lteBind}");
        } elseif (null !== $gte) {
            $gteBind = $this->bind($gte);
            $this->queryBuilder->andWhere("{$field} >= :{$gteBind}");
        } elseif (null !== $lte) {
            $lteBind = $this->bind($lte);
            $this->queryBuilder->andWhere("{$field} <= :{$lteBind}");
        }

        return $this;
    }

    public function rangeDateTime(
        string $field,
        ?\DateTimeInterface $gte,
        ?\DateTimeInterface $lte
    ): self {
        $this->range(
            $field,
            $gte?->format('Y-m-d H:i:s'),
            $lte?->format('Y-m-d H:i:s')
        );

        return $this;
    }

    private function bind(mixed $value): string
    {
        $bind = 'bind_' . md5(serialize($value) . random_bytes(10));

        if (is_array($value)) {
            $this->queryBuilder->setParameter($bind, $value, Connection::PARAM_STR_ARRAY);
        } else {
            $this->queryBuilder->setParameter($bind, $value);
        }

        return $bind;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }
}
