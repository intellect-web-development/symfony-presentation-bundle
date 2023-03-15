<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\ArgumentResolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use IWD\Symfony\PresentationBundle\Dto\Input\SearchQuery;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\FiltersMakerInterface;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\PaginationMakerInterface;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\SortsMakerInterface;

class SearchQueryResolver implements ValueResolverInterface
{
    public function __construct(
        private FiltersMakerInterface $filtersMaker,
        private PaginationMakerInterface $paginationMaker,
        private SortsMakerInterface $sortsMaker,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        yield new SearchQuery(
            $this->paginationMaker::make($request),
            $this->filtersMaker::make($request),
            $this->sortsMaker::make($request)
        );
    }

    private function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return SearchQuery::class === $argument->getType();
    }
}
