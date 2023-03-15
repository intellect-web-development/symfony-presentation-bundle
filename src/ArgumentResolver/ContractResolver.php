<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\ArgumentResolver;

use Generator;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use IWD\Symfony\PresentationBundle\Interfaces\InputContractInterface;
use IWD\Symfony\PresentationBundle\Service\Factory\Interfaces\InputContractFactoryInterface;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\RequestParserInterface;

class ContractResolver implements ValueResolverInterface
{
    public function __construct(
        private InputContractFactoryInterface $inputContractResolver,
        private RequestParserInterface $requestParser
    ) {
    }

    /**
     * @throws JsonException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        /** @var class-string<InputContractInterface> $type */
        $type = $argument->getType();

        yield $this->inputContractResolver->resolve(
            $type,
            $this->requestParser->parse($request)
        );
    }

    private function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();

        return null !== $type && is_subclass_of($type, InputContractInterface::class);
    }
}
