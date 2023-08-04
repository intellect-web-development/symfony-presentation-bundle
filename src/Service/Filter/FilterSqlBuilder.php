<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\Filter;

use DateTimeInterface;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\QueryBuilder;
use IWD\Symfony\PresentationBundle\Dto\Input\Pagination;
use IWD\Symfony\PresentationBundle\Dto\Input\Sort;
use IWD\Symfony\PresentationBundle\Dto\Input\Sorts;

class FilterSqlBuilder
{
    public QueryBuilder $queryBuilder;
    private string $alias;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $aliases = $this->queryBuilder->getAllAliases();
        if (0 !== count($aliases)) {
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

    public function equals(string $field, mixed $value, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (null !== $value) {
            $value = is_bool($value) ? (int) $value : $value;
            $bind = $this->bind($value);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("{$field} = :{$bind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("{$field} = :{$bind}");
            }
        }

        return $this;
    }

    public function notEquals(string $field, mixed $value, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (null !== $value) {
            $value = is_bool($value) ? (int) $value : $value;
            $bind = $this->bind($value);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("{$field} != :{$bind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("{$field} != :{$bind}");
            }
        }

        return $this;
    }

    public function like(string $field, mixed $value, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (!empty($value)) {
            $value = mb_strtolower($value);
            $bind = $this->bind("%{$value}%");
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("LOWER({$field}) LIKE :{$bind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("LOWER({$field}) LIKE :{$bind}");
            }
        }

        return $this;
    }

    public function isNull(string $field, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (FilterStrategy::And === $filterStrategy) {
            $this->queryBuilder->andWhere("{$field} IS NULL");
        }
        if (FilterStrategy::Or === $filterStrategy) {
            $this->queryBuilder->orWhere("{$field} IS NULL");
        }

        return $this;
    }

    public function notNull(string $field, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (FilterStrategy::And === $filterStrategy) {
            $this->queryBuilder->andWhere("{$field} IS NOT NULL");
        }
        if (FilterStrategy::Or === $filterStrategy) {
            $this->queryBuilder->orWhere("{$field} IS NOT NULL");
        }

        return $this;
    }

    public function notLike(string $field, mixed $value, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (!empty($value)) {
            $value = mb_strtolower($value);
            $bind = $this->bind("%{$value}%");
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("LOWER({$field}) NOT LIKE :{$bind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("LOWER({$field}) NOT LIKE :{$bind}");
            }
        }

        return $this;
    }

    public function in(string $field, ?array $values, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (!empty($values)) {
            $bind = $this->bind($values);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere(sprintf('%s IN (:%s)', $field, $bind));
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere(sprintf('%s IN (:%s)', $field, $bind));
            }
        }

        return $this;
    }

    public function notIn(string $field, ?array $values, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (!empty($values)) {
            $bind = $this->bind($values);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("{$field} NOT IN (:{$bind})");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("{$field} NOT IN (:{$bind})");
            }
        }

        return $this;
    }

    public function lessThan(string $field, mixed $lte, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (null !== $lte) {
            $lteBind = $this->bind($lte);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("{$field} < :{$lteBind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("{$field} < :{$lteBind}");
            }
        }

        return $this;
    }

    public function greaterThan(string $field, mixed $gte, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (null !== $gte) {
            $gteBind = $this->bind($gte);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("{$field} > :{$gteBind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("{$field} > :{$gteBind}");
            }
        }

        return $this;
    }

    public function lessOrEquals(string $field, mixed $lte, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (null !== $lte) {
            $lteBind = $this->bind($lte);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("{$field} <= :{$lteBind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("{$field} <= :{$lteBind}");
            }
        }

        return $this;
    }

    public function greaterOrEquals(string $field, mixed $gte, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (null !== $gte) {
            $gteBind = $this->bind($gte);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("{$field} >= :{$gteBind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("{$field} >= :{$gteBind}");
            }
        }

        return $this;
    }

    public function range(string $field, mixed $gte, mixed $lte, FilterStrategy $filterStrategy = FilterStrategy::And): self
    {
        if (null !== $gte && null !== $lte) {
            $gteBind = $this->bind($gte);
            $lteBind = $this->bind($lte);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("{$field} BETWEEN :{$gteBind} AND :{$lteBind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("{$field} BETWEEN :{$gteBind} AND :{$lteBind}");
            }
        } elseif (null !== $gte) {
            $gteBind = $this->bind($gte);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("{$field} >= :{$gteBind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("{$field} >= :{$gteBind}");
            }
        } elseif (null !== $lte) {
            $lteBind = $this->bind($lte);
            if (FilterStrategy::And === $filterStrategy) {
                $this->queryBuilder->andWhere("{$field} <= :{$lteBind}");
            }
            if (FilterStrategy::Or === $filterStrategy) {
                $this->queryBuilder->orWhere("{$field} <= :{$lteBind}");
            }
        }

        return $this;
    }

    public function rangeDateTime(
        string $field,
        ?DateTimeInterface $gte,
        ?DateTimeInterface $lte,
        FilterStrategy $filterStrategy = FilterStrategy::And
    ): self {
        $this->range(
            $field,
            $gte?->format('Y-m-d H:i:s'),
            $lte?->format('Y-m-d H:i:s'),
            $filterStrategy,
        );

        return $this;
    }

    private function bind(mixed $value): string
    {
        $bind = 'bind_' . md5(serialize($value) . random_bytes(10));

        if (is_array($value)) {
            $this->queryBuilder->setParameter($bind, $value, ArrayParameterType::STRING);
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
