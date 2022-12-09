<?php

declare(strict_types=1);

namespace IWD\Symfony\PresentationBundle\Dto\Input;

use IWD\Symfony\PresentationBundle\Exception\DomainException;

class OutputFormat
{
    public const JSON = 'json';
    public const XML = 'xml';
    public const YAML = 'yaml';
    public const CSV = 'csv';

    public const VALID_FORMATS = [
        self::JSON,
        self::XML,
        self::YAML,
        self::CSV,
    ];

    protected string $format;

    public function __construct(string $format)
    {
        self::guardFormat($format);
        $this->format = $format;
    }

    public static function guardFormat(string $format): void
    {
        if (!in_array($format, self::VALID_FORMATS)) {
            throw new DomainException("{$format} is invalid format type");
        }
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
