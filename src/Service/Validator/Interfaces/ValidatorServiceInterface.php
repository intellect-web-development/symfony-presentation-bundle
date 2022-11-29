<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\Validator\Interfaces;

use Symfony\PresentationBundle\Exception\ValidatorException;

interface ValidatorServiceInterface
{
    /**
     * @throws ValidatorException
     */
    public function validate(object $object): void;
}
