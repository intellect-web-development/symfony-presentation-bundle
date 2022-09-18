<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\Validator\Implementations;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\PresentationBundle\Exception\ValidatorException;
use Symfony\PresentationBundle\Service\Validator\Interfaces\ValidatorServiceInterface;

class ValidatorService implements ValidatorServiceInterface
{
    private ValidatorInterface $validator;
    private SerializerInterface $serializer;

    public function __construct(
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ) {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    public function validate(object $object): void
    {
        /** @var ConstraintViolationList $violationList */
        $violationList = $this->validator->validate($object);
        $errors = [];
        foreach ($violationList as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        if ($violationList->count()) {
            $errorJson = $this->serializer->serialize($errors, 'json');
            throw new ValidatorException($errorJson);
        }
    }
}
