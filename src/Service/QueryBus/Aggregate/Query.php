<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\QueryBus\Aggregate;

use Symfony\PresentationBundle\Dto\Input\Filters;

class Query
{
    public string $aggregateId;
    /** @var class-string */
    public string $targetEntityClass;
    public array $hints;
    public Filters $filters;
    public bool $eager;

    /**
     * @param class-string $targetEntityClass
     */
    public function __construct(
        string $aggregateId,
        string $targetEntityClass,
        array $hints = [],
        bool $eager = false,
        Filters $filters = null
    ) {
        $this->targetEntityClass = $targetEntityClass;
        $this->hints = $hints;
        $this->filters = $filters ?? new Filters();
        $this->aggregateId = $aggregateId;
        $this->eager = $eager;
    }
}
