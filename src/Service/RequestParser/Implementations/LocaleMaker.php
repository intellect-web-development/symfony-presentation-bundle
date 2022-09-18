<?php

declare(strict_types=1);

namespace Symfony\PresentationBundle\Service\RequestParser\Implementations;

use Symfony\Component\HttpFoundation\Request;
use Symfony\PresentationBundle\Service\RequestParser\Interfaces\LocaleMakerInterface;
use Symfony\PresentationBundle\Dto\Input\Locale;

class LocaleMaker implements LocaleMakerInterface
{
    public const LOCALE_QUERY_PARAM = 'lang';

    public static function make(Request $request): Locale
    {
        $languages = [];
        if ($request->query->has(self::LOCALE_QUERY_PARAM)) {
            $languages[] = $request->query->get(self::LOCALE_QUERY_PARAM);
        }

        if ($preferredLanguage = $request->getPreferredLanguage()) {
            $languages[] = $preferredLanguage;
        }

        if ($acceptLanguage = $request->headers->get('Accept-Language')) {
            foreach (explode(',', $acceptLanguage) as $langItem) {
                $result = strstr($langItem, ';', true);
                $languages[] = ($result === false) ? $langItem : $result;
            }
        }
        $languages[] = $request->getDefaultLocale();

        /** @var array<int, string> $languages */
        foreach ($languages as $i => $language) {
            $languages[$i] = str_replace('_', '-', $language);
        }
        $languages = array_unique($languages);
        if (empty($languages)) {
            $languages = ['en'];
        }

        return new Locale($languages);
    }
}
