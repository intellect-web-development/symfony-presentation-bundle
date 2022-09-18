<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Dto\Input;

use TypeError;

class Sorts
{
    /** @var Sort[] */
    protected array $sorts;

    /**
     * Sorts constructor.
     * @param Sort[] $sorts
     */
    public function __construct(array $sorts = [])
    {
        foreach ($sorts as $sort) {
            if (!$sort instanceof Sort) {
                throw new TypeError('Variable is not ' . Sort::class);
            }
        }
        $this->sorts = $sorts;
    }

    public function add(Sort $sort): void
    {
        $this->sorts[] = $sort;
    }

    /**
     * @return Sort[]
     */
    public function toArray(): array
    {
        return $this->sorts;
    }
}
