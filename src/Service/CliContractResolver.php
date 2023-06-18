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
     * @template T of object
     *
     * @param InputInterface $input
     * @param class-string<T> $contractClass
     * @return T
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
