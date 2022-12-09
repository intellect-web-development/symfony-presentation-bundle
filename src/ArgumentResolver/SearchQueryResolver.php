<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use IWD\Symfony\PresentationBundle\Dto\Input\SearchQuery;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\FiltersMakerInterface;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\PaginationMakerInterface;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\SortsMakerInterface;

class SearchQueryResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private FiltersMakerInterface $filtersMaker,
        private PaginationMakerInterface $paginationMaker,
        private SortsMakerInterface $sortsMaker,
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return SearchQuery::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        yield new SearchQuery(
            $this->paginationMaker::make($request),
            $this->filtersMaker::make($request),
            $this->sortsMaker::make($request)
        );
    }
}
