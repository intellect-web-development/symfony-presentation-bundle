<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\ArgumentResolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\PresentationBundle\Interfaces\InputContractInterface;
use Symfony\PresentationBundle\Service\Factory\Interfaces\InputContractFactoryInterface;
use Symfony\PresentationBundle\Service\RequestParser\Interfaces\RequestParserInterface;

class ContractResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private InputContractFactoryInterface $inputContractResolver,
        private RequestParserInterface        $requestParser
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();
        return $type !== null && is_subclass_of($type, InputContractInterface::class);
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return Generator
     * @throws \JsonException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        /** @var class-string<InputContractInterface> $type */
        $type = $argument->getType();

        yield $this->inputContractResolver->resolve(
            $type,
            $this->requestParser->parse($request)
        );
    }
}
