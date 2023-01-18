<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Exception;

use Throwable;

class DeserializePayloadToInputContractException extends PresentationBundleException
{
    public function __construct(
        string $message = '',
        ?int $code = 500,
        ?Throwable $previous = null,
        private ?array $payload = null,
    ) {
        parent::__construct($message, (int) $code, $previous);
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }
}
