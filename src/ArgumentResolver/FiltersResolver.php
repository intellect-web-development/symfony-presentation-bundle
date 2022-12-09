<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use IWD\Symfony\PresentationBundle\Dto\Input\Filters;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\FiltersMakerInterface;

class FiltersResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private FiltersMakerInterface $filtersMaker
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return Filters::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        yield $this->filtersMaker::make($request);
    }
}
