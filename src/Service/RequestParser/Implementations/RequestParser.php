<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\RequestParser\Implementations;

use Symfony\Component\HttpFoundation\Request;
use Symfony\PresentationBundle\Service\RequestParser\Interfaces\RequestParserInterface;

class RequestParser implements RequestParserInterface
{
    /**
     * @return array<string, string>
     *
     * @throws \JsonException
     */
    public function parse(Request $request): array
    {
        $query = $request->query->all();

        $content = !empty($request->getContent())
            ? (array) json_decode(
                (string) $request->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            )
            : []
        ;

        $requestData = $request->request->all();

        /** @var array<string, string> $payload */
        $payload = array_merge($query, $content, $requestData);

        return $payload;
    }
}
