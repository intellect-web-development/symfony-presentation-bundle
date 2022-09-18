<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\Presenter\Interfaces;

use Symfony\Component\HttpFoundation\Response;
use Symfony\PresentationBundle\Dto\Input\OutputFormat;

interface PresenterInterface
{
    public function present(
        array $data,
        array $headers = [],
        OutputFormat $outputFormat = null
    ): Response;
}
