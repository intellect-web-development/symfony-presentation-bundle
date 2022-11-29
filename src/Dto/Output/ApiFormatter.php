<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Dto\Output;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class ApiFormatter
{
    /**
     * HTTP response status codes (https://developer.mozilla.org/en-US/docs/Web/HTTP/Status).
     *
     * @OA\Property(type="integer", example=200)
     */
    public int $status;

    /**
     * JSON Payload.
     *
     * @var array<string, string>
     *
     * @OA\Property(type="object")
     */
    public array $data;

    /**
     * Information about the success of the operation.
     *
     * @OA\Property(type="boolean", example=true)
     */
    public bool $ok;

    /**
     * Text Payload.
     *
     * @var array<string, string>
     *
     * @OA\Property(type="object")
     */
    public array $messages;

    /**
     * ApiFormatter constructor.
     *
     * @param array<string, string> $data
     * @param array<string, string> $messages
     */
    public function __construct(
        array $data = [],
        int $status = Response::HTTP_OK,
        array $messages = []
    ) {
        $this->status = $status;
        $this->ok = $status >= 200 && $status < 300;
        $this->data = $data;
        $this->messages = $messages;
    }

    public function toArray(): array
    {
        return self::prepare(
            $this->data,
            $this->status,
            $this->messages
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function prepare(
        mixed $data = [],
        int $status = Response::HTTP_OK,
        mixed $messages = []
    ): array {
        return [
            'status' => $status,
            'ok' => $status >= 200 && $status < 300,
            'data' => $data,
            'messages' => $messages,
        ];
    }
}
