<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\RequestParser\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use Symfony\PresentationBundle\Dto\Input\Pagination;

interface PaginationMakerInterface
{
    public static function make(Request $request): Pagination;
}
