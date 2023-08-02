<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Dto\Input;

use IWD\Symfony\PresentationBundle\Service\Filter\FilterStrategy;
use OpenApi\Annotations as OA;

class SearchQuery
{
    /**
     * @OA\Property(
     *     property="filter",
     *     type="object",
     *     example={"propertyName_1": {"like": "value_1"}, "propertyName_2": {"eq": "value_2"}}
     * )
     */
    public Filters $filters;

    /**
     * @OA\Property(property="sort", type="string", example="-createdAt")
     */
    public Sorts $sorts;

    /**
     * @OA\Property(property="page", type="object", example={"number": 1, "size": 20})
     */
    public Pagination $pagination;

    /**
     * @OA\Property(property="strategy", type="string", example="and/or")
     */
    public FilterStrategy $baseFilterStrategy;

    public function __construct(
        Pagination $pagination,
        Filters $filters,
        Sorts $sorts,
        FilterStrategy $baseFilterStrategy = FilterStrategy::And,
    ) {
        $this->pagination = $pagination;
        $this->filters = $filters;
        $this->sorts = $sorts;
        $this->baseFilterStrategy = $baseFilterStrategy;
    }
}
