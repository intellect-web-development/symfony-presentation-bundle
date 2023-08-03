<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Attribute;

use Attribute;
use IWD\Symfony\PresentationBundle\Interfaces\InputContractInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class CliContract
{
    public function __construct(
        /** @var class-string<InputContractInterface> */
        public string $class
    ) {
    }
}
