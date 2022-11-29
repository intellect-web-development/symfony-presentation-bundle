<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\PresentationBundle\Dto\Input\Pagination;
use Symfony\PresentationBundle\Service\RequestParser\Interfaces\PaginationMakerInterface;

class PaginationResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private PaginationMakerInterface $paginationMaker
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return Pagination::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        yield $this->paginationMaker::make($request);
    }
}
