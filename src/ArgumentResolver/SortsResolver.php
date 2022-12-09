<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use IWD\Symfony\PresentationBundle\Dto\Input\Sorts;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\SortsMakerInterface;

class SortsResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private SortsMakerInterface $sortMaker
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return Sorts::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        yield $this->sortMaker::make($request);
    }
}
