<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\Validator\Implementations;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use IWD\Symfony\PresentationBundle\Exception\ValidatorException;
use IWD\Symfony\PresentationBundle\Service\Validator\Interfaces\ValidatorServiceInterface;

class ValidatorService implements ValidatorServiceInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {
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
            $errorJson = $this->serializer->serialize($errors, 'json', [
                'json_encode_options' => JSON_UNESCAPED_UNICODE
            ]);
            throw new ValidatorException($errorJson);
        }
    }
}
