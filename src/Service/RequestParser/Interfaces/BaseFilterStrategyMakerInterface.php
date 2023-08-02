<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces;

use IWD\Symfony\PresentationBundle\Service\Filter\FilterStrategy;
use Symfony\Component\HttpFoundation\Request;

interface BaseFilterStrategyMakerInterface
{
    public static function make(Request $request): FilterStrategy;
}
