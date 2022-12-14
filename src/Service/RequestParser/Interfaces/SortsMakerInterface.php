<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use IWD\Symfony\PresentationBundle\Dto\Input\Sorts;

interface SortsMakerInterface
{
    public static function make(Request $request): Sorts;
}
