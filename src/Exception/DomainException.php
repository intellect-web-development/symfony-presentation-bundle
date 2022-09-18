<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Exception;

use Throwable;

class DomainException extends PresentationBundleException
{
    public function __construct(
        string $message = "",
        ?int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, (int) $code, $previous);
    }
}
