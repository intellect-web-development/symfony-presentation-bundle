<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\Factory\Interfaces;

use Symfony\PresentationBundle\Interfaces\InputContractInterface;

interface InputContractFactoryInterface
{
    /**
     * @param class-string<InputContractInterface> $contractClass
     * @param array<string, string>                $payload
     */
    public function resolve(string $contractClass, array $payload): InputContractInterface;
}
