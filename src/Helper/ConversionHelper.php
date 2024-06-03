<?php

namespace App\Helper;

use Symfony\Component\Intl\Countries;

class ConversionHelper
{
    public static function countryNameToAlpha2(string $countryName): ?string
    {
        $countryName = ucwords(strtolower($countryName));
        foreach (Countries::getNames() as $alpha2 => $name) {
            if (strcasecmp($name, $countryName) === 0) {
                return $alpha2;
            }
        }
        return null;
    }
}