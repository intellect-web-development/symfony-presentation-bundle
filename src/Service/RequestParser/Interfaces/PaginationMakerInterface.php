<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use IWD\Symfony\PresentationBundle\Dto\Input\Pagination;

interface PaginationMakerInterface
{
    public static function make(Request $request): Pagination;
}
