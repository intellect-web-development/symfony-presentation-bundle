<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service;

use IWD\Symfony\PresentationBundle\Interfaces\InputContractInterface;
use IWD\Symfony\PresentationBundle\Service\Factory\Interfaces\InputContractFactoryInterface;
use Symfony\Component\Console\Input\InputInterface;

class CliContractResolver
{
    public function __construct(
        private readonly InputContractFactoryInterface $inputContractResolver,
    ) {
    }

    /**
     * @param class-string<InputContractInterface> $contractClass
     */
    public function resolve(InputInterface $input, string $contractClass): InputContractInterface
    {
        /** @var array<string, string> $payload */
        $payload = array_merge(
            $input->getOptions(),
            $input->getArguments(),
        );

        return $this->inputContractResolver->resolve(
            $contractClass,
            $payload
        );
    }
}
