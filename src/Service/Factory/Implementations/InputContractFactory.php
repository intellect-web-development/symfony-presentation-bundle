<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\Factory\Implementations;

use IWD\Symfony\PresentationBundle\Exception\DeserializePayloadToInputContractException;
use IWD\Symfony\PresentationBundle\Exception\PresentationBundleException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
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
     * @throws PresentationBundleException
     * @throws \JsonException
     */
    public function resolve(string $contractClass, array $payload): InputContractInterface
    {
        if (!is_subclass_of($contractClass, InputContractInterface::class)) {
            throw new PresentationBundleException("{$contractClass} not is subclass of " . InputContractInterface::class, 400);
        }

        try {
            $inputContractDto = $this->serializer->deserialize(
                json_encode($payload, JSON_THROW_ON_ERROR),
                $contractClass,
                'json'
            );
        } catch (NotNormalizableValueException $exception) {
            throw new DeserializePayloadToInputContractException(
                message: 'Not normalizable value. Check that required fields are passed and they are not null, and fields type.',
                code: 400,
                previous: $exception,
                payload: $payload
            );
        }

        $this->validator->validate($inputContractDto);

        return $inputContractDto;
    }
}
