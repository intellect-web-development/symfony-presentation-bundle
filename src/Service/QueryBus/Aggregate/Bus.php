<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\QueryBus\Aggregate;

use Symfony\PresentationBundle\Service\Filter\Fetcher;

class Bus
{
    public function __construct(
        private Fetcher $fetcher
    ) {
    }

    public function query(Query $actionContext): object
    {
        return $this->fetcher->getById(
            context: ($this->fetcher->createContext($actionContext->targetEntityClass))->addFilters($actionContext->filters),
            id: $actionContext->aggregateId,
            eager: $actionContext->eager,
            hints: $actionContext->hints
        );
    }

    public function getRelationPlan(Query $actionContext): array
    {
        return $this->fetcher->createAssocRelationMap(
            context: ($this->fetcher->createContext($actionContext->targetEntityClass))->addFilters($actionContext->filters),
            hints: $actionContext->hints
        );
    }
}
