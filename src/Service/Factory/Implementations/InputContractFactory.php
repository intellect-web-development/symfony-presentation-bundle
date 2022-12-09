<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\Factory\Implementations;

use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use IWD\Symfony\PresentationBundle\Exception\DomainException;
use IWD\Symfony\PresentationBundle\Interfaces\InputContractInterface;
use IWD\Symfony\PresentationBundle\Service\Factory\Interfaces\InputContractFactoryInterface;
use IWD\Symfony\PresentationBundle\Service\Validator\Interfaces\ValidatorServiceInterface;

class InputContractFactory implements InputContractFactoryInterface
{
    private ValidatorServiceInterface $validator;
    private SerializerInterface $serializer;

    public function __construct(
        ValidatorServiceInterface $validator,
        SerializerInterface $serializer
    ) {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * @param class-string<InputContractInterface> $contractClass
     * @param array<string, string>                $payload
     *
     * @throws DomainException
     * @throws \JsonException
     */
    public function resolve(string $contractClass, array $payload): InputContractInterface
    {
        if (!is_subclass_of($contractClass, InputContractInterface::class)) {
            throw new DomainException("{$contractClass} not is subclass of " . InputContractInterface::class, 400);
        }

        try {
            $inputContractDto = $this->serializer->deserialize(
                json_encode($payload, JSON_THROW_ON_ERROR),
                $contractClass,
                'json'
            );
        } catch (NotNormalizableValueException $exception) {
            throw new DomainException(
                'JSON parse error. Check that required fields are passed and they are not null, and fields type',
                400
            );
        }

        $this->validator->validate($inputContractDto);

        return $inputContractDto;
    }
}
