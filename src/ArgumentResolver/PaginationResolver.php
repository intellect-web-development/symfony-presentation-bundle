<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\ArgumentResolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use IWD\Symfony\PresentationBundle\Dto\Input\Pagination;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\PaginationMakerInterface;

class PaginationResolver implements ValueResolverInterface
{
    public function __construct(
        private PaginationMakerInterface $paginationMaker
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        yield $this->paginationMaker::make($request);
    }

    private function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return Pagination::class === $argument->getType();
    }
}
