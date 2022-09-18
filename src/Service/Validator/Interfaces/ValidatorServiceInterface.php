<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\Validator\Interfaces;

use Symfony\PresentationBundle\Exception\ValidatorException;

interface ValidatorServiceInterface
{
    /**
     * @param object $object
     * @return void
     * @throws ValidatorException
     */
    public function validate(object $object): void;
}
