<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\QueryBus\Aggregate;

use IWD\Symfony\PresentationBundle\Service\Filter\Fetcher;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Bus
{
    public function __construct(
        private Fetcher $fetcher,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function query(Query $query): object
    {
        return $this->fetcher->getById(
            context: ($this->fetcher->createContext($query->targetEntityClass))->addFilters($query->filters),
            id: $query->aggregateId,
            eager: $query->eager,
            hints: $query->hints
        );
    }

    public function grantedQuery(
        Query $query,
        string $grantedAttribute,
        string $accessDeniedMessage = 'Access Denied.'
    ): object {
        $entity = $this->query($query);
        if (!$this->authorizationChecker->isGranted($grantedAttribute, $entity)) {
            throw new AccessDeniedException($accessDeniedMessage);
        }

        return $entity;
    }

    public function getRelationPlan(Query $actionContext): array
    {
        return $this->fetcher->createAssocRelationMap(
            context: ($this->fetcher->createContext($actionContext->targetEntityClass))->addFilters($actionContext->filters),
            hints: $actionContext->hints
        );
    }
}
