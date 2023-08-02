<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\RequestParser\Implementations;

use IWD\Symfony\PresentationBundle\Service\Filter\FilterMode;
use IWD\Symfony\PresentationBundle\Service\Filter\FilterStrategy;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\BaseFilterStrategyMakerInterface;
use Symfony\Component\HttpFoundation\Request;
use IWD\Symfony\PresentationBundle\Dto\Input\Filter;
use IWD\Symfony\PresentationBundle\Dto\Input\Filters;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\FiltersMakerInterface;

class BaseFilterStrategyMaker implements BaseFilterStrategyMakerInterface
{
    public static function make(Request $request): FilterStrategy
    {
        $strategy = $request->query->get('strategy', FilterStrategy::And->value);

        return FilterStrategy::tryFrom(mb_strtolower((string) $strategy)) ?? FilterStrategy::And;
    }
}
