<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\ArgumentResolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\PresentationBundle\Dto\Input\Locale;
use Symfony\PresentationBundle\Service\RequestParser\Interfaces\LocaleMakerInterface;

class LocaleResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private LocaleMakerInterface $localeMaker
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Locale::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield $this->localeMaker::make($request);
    }
}
