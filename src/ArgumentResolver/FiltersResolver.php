<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\ArgumentResolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\PresentationBundle\Dto\Input\Filters;
use Symfony\PresentationBundle\Service\RequestParser\Interfaces\FiltersMakerInterface;

class FiltersResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private FiltersMakerInterface $filtersMaker
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Filters::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield $this->filtersMaker::make($request);
    }
}
