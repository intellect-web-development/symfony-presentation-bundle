<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Dto\Output;

class SearchResult
{
    public array $entities;
    public OutputPagination $pagination;

    public function __construct(array $entities, OutputPagination $pagination)
    {
        $this->entities = $entities;
        $this->pagination = $pagination;
    }
}
