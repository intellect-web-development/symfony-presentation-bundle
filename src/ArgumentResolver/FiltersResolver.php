<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\ArgumentResolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use IWD\Symfony\PresentationBundle\Dto\Input\Filters;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\FiltersMakerInterface;

class FiltersResolver implements ValueResolverInterface
{
    public function __construct(
        private FiltersMakerInterface $filtersMaker
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        yield $this->filtersMaker::make($request);
    }

    private function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return Filters::class === $argument->getType();
    }
}
