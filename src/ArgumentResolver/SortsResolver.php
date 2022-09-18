<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\ArgumentResolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\PresentationBundle\Dto\Input\Sorts;
use Symfony\PresentationBundle\Service\RequestParser\Interfaces\SortsMakerInterface;

class SortsResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private SortsMakerInterface $sortMaker
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Sorts::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield $this->sortMaker::make($request);
    }
}
