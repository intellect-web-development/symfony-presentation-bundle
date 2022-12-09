<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\QueryBus\Search;

use IWD\Symfony\PresentationBundle\Dto\Input\Filters;
use IWD\Symfony\PresentationBundle\Dto\Input\Pagination;
use IWD\Symfony\PresentationBundle\Dto\Input\Sorts;

class Query
{
    /** @var class-string */
    public string $targetEntityClass;
    public Pagination $pagination;
    public Filters $filters;
    public Sorts $sorts;
    public bool $eager;
    public array $hints;

    /**
     * Context constructor.
     *
     * @param class-string $targetEntityClass
     */
    public function __construct(
        string $targetEntityClass,
        array $hints = [],
        Pagination $pagination = null,
        Filters $filters = null,
        Sorts $sorts = null,
        bool $eager = true,
    ) {
        $this->targetEntityClass = $targetEntityClass;
        $this->pagination = $pagination ?? new Pagination();
        $this->filters = $filters ?? new Filters();
        $this->sorts = $sorts ?? new Sorts();
        $this->eager = $eager;
        $this->hints = $hints;
    }
}
