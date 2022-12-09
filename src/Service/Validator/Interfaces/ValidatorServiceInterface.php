<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Service\Validator\Interfaces;

use IWD\Symfony\PresentationBundle\Exception\ValidatorException;

interface ValidatorServiceInterface
{
    /**
     * @throws ValidatorException
     */
    public function validate(object $object): void;
}
