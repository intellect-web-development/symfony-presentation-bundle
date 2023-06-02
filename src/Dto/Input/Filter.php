<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Dto\Input;

use IWD\Symfony\PresentationBundle\Service\Filter\FilterMode;

class Filter
{
    public function __construct(
        public string $property,
        public FilterMode $mode,
        public mixed $value = null
    ) {
    }
}
