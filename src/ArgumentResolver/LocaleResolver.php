<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use IWD\Symfony\PresentationBundle\Dto\Input\Locale;
use IWD\Symfony\PresentationBundle\Service\RequestParser\Interfaces\LocaleMakerInterface;

class LocaleResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private LocaleMakerInterface $localeMaker
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return Locale::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        yield $this->localeMaker::make($request);
    }
}
