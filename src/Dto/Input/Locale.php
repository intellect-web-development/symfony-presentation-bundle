<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Dto\Input;

use IWD\Symfony\PresentationBundle\Exception\PresentationBundleException;

class Locale
{
    /**
     * @param string[] $locales
     */
    public function __construct(
        public array $locales = []
    ) {
        if (empty($locales)) {
            throw new PresentationBundleException('Locales is not set');
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
