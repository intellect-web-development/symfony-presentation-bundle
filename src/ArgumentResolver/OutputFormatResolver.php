<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\ArgumentResolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\PresentationBundle\Dto\Input\OutputFormat;

class OutputFormatResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === OutputFormat::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $format = $request->attributes->get('_format', 'json');
        yield new OutputFormat($format);
    }
}
