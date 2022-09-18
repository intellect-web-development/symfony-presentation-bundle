<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Dto\Input;

class Filter
{
    private string $property;
    private mixed $value;
    private string $searchMode;

    public function __construct(
        string $property,
        string $mode,
        mixed $value = null
    ) {

        $this->property = $property;
        $this->value = $value;
        $this->searchMode = mb_strtolower($mode);
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getSearchMode(): string
    {
        return $this->searchMode;
    }

    public function setPropertyName(string $property): void
    {
        $this->property = $property;
    }
}
