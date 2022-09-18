<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Dto\Input;

use Symfony\PresentationBundle\Exception\DomainException;

class Locale
{
    /** @var string[] */
    public array $locales = [];

    public function __construct(array $locales)
    {
        if (empty($locales)) {
            throw new DomainException('Locales is not set');
        }
        $this->locales[] = $locales;
    }

    public function getPriorityLang(): string
    {
        return current($this->locales);
    }

    public function getAll(): array
    {
        return $this->locales;
    }
}
