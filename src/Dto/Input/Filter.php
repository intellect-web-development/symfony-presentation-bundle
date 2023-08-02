<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Dto\Input;

use IWD\Symfony\PresentationBundle\Service\Filter\FilterMode;
use IWD\Symfony\PresentationBundle\Service\Filter\FilterStrategy;

class Filter
{
    public function __construct(
        public string $property,
        public FilterMode $mode,
        public mixed $value = null,
        public FilterStrategy $strategy = FilterStrategy::And,
    ) {
    }
}
