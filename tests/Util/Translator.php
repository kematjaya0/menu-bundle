<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\MenuBundle\Tests\Util;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @package Kematjaya\URLBundle\Tests\Util
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Translator implements TranslatorInterface 
{
    
    /**
     * Translates a message.
     *
     * @param string $id The message id (may also be an object that can be converted to a string)
     * @param array<string, mixed> $parameters An array of parameters to replace in the message
     * @param string|null $domain The domain for the message or null to use the default domain
     * @param string|null $locale The locale for the message or null to use the default locale
     * @return string The translated message
     */
    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $id;
    }

    public function getLocale(): string
    {
        return "EN";
    }
}
