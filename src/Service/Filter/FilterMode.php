<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\Filter;

enum FilterMode: string
{
    case In = 'in';
    case Like = 'like';
    case Range = 'range';
    case NotIn = 'not-in';
    case Equals = 'equals';
    case EqualsAlias1 = 'eq';
    case EqualsAlias2 = '=';
    case IsNull = 'is-null';
    case NotLike = 'not-like';
    case NotNull = 'not-null';
    case LessThan = 'less-than';
    case LessThanAlias1 = '<';
    case LessThanAlias2 = 'lt';
    case NotEquals = 'not-equals';
    case NotEqualsAlias1 = '!=';
    case NotEqualsAlias2 = '<>';
    case NotEqualsAlias3 = 'neq';
    case GreaterThan = 'greater-than';
    case GreaterThanAlias1 = '>';
    case GreaterThanAlias2 = 'gt';
    case LessOrEquals = 'less-or-equals';
    case LessOrEqualsAlias1 = '<=';
    case LessOrEqualsAlias2 = 'lte';
    case GreaterOrEquals = 'greater-or-equals';
    case GreaterOrEqualsAlias1 = '>=';
    case GreaterOrEqualsAlias2 = 'gte';
}
