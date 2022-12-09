<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Dto\Input;

use Symfony\PresentationBundle\Exception\DomainException;

class Locale
{
    /**
     * @param string[] $locales
     */
    public function __construct(
        public array $locales = []
    ) {
        if (empty($locales)) {
            throw new DomainException('Locales is not set');
        }
    }

    public function getPriorityLang(): string
    {
        if (empty($this->locales)) {
            return 'en';
        }
        return current($this->locales);
    }

    public function getAll(): array
    {
        return $this->locales;
    }
}
