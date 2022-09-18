<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\RequestParser\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use Symfony\PresentationBundle\Dto\Input\Filters;

interface FiltersMakerInterface
{
    public static function make(Request $request): Filters;
}