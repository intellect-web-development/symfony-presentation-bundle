<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Exception;

use Throwable;

class ValidatorException extends PresentationBundleException
{
    public function __construct(
        string $message = '',
        ?int $code = 400,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, (int) $code, $previous);
    }
}
