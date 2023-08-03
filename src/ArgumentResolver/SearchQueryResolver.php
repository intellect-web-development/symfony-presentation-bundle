<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\ArgumentResolver;

use Generator;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\BaseFilterStrategyMakerInterface;
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
        private readonly FiltersMakerInterface $filtersMaker,
        private readonly PaginationMakerInterface $paginationMaker,
        private readonly SortsMakerInterface $sortsMaker,
        private readonly BaseFilterStrategyMakerInterface $baseFilterStrategyMaker,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        $searchQuery = new SearchQuery(
            pagination: $this->paginationMaker::make($request),
            filters: $this->filtersMaker::make($request),
            sorts: $this->sortsMaker::make($request),
            baseFilterStrategy: $this->baseFilterStrategyMaker::make($request),
        );

        foreach ($searchQuery->filters->toArray() as $filter) {
            $filter->strategy = $searchQuery->baseFilterStrategy;
        }

        yield $searchQuery;
    }

    private function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return SearchQuery::class === $argument->getType();
    }
}
