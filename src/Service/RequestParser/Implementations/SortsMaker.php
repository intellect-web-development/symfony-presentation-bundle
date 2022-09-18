<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\RequestParser\Implementations;

use Symfony\Component\HttpFoundation\Request;
use Symfony\PresentationBundle\Dto\Input\Sort;
use Symfony\PresentationBundle\Dto\Input\Sorts;
use Symfony\PresentationBundle\Service\RequestParser\Interfaces\SortsMakerInterface;

class SortsMaker implements SortsMakerInterface
{
    public static function make(Request $request): Sorts
    {
        /** @var string $sortRaw */
        $sortRaw = $request->query->get('sort', '');
        if (empty($sortRaw)) {
            return new Sorts();
        }

        $sortParams = explode(
            ',',
            str_replace(' ', '', $sortRaw)
        );

        $sorts = [];
        foreach ($sortParams as $sortParam) {
            $field = trim($sortParam, '-');

            $direction = $sortParam[0] === '-' ? 'DESC' : 'ASC';
            $sorts[] = new Sort($field, $direction);
        }

        return new Sorts($sorts);
    }
}
