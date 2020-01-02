<?php

namespace Kelvinho\Virus;

class Timezone {
    private static array $descriptions = array(
        -12 => "No one",
        -11 => "American Samoa, New Zealand",
        -10 => "Alaska, Hawaii, New Zealand",
        -9 => "Alaska",
        -8 => "Pacific time, British Columbia, California",
        -7 => "Alberta, Arizona, New Mexico",
        -6 => "Central time, Ontario, Chile, Mexico, Texas",
        -5 => "Brazil, New York, Toronto",
        -4 => "Bolivia, Chile, Venezuela",
        -3 => "Argentina, Greenland",
        -2 => "No one",
        -1 => "Tiny chunk of Greenland",
        0 => "UK, Portugal, Ireland",
        1 => "France, Norway, Germany",
        2 => "Finland, Ukraine, Libya, Sudan",
        3 => "Moscow, Saudi Arabia, Turkey",
        4 => "UAE, Azerbaijan",
        5 => "Uzbekistan, Kazakhstan",
        6 => "Bangladesh, Omsk Oblast",
        7 => "Laos, Cambodia, Vietnam",
        8 => "Taiwan, Malaysia, China, Mongolia",
        9 => "Japan, Korea, Indonesia",
        10 => "Australia, Papua New Guinea, Guam",
        11 => "Solomon Islands, Vanuatu",
        12 => "New Zealand, Marshal Islands");

    public static function okay(int $timezone): bool {
        return isset(self::$descriptions[$timezone]);
    }

    public static function getDescription(int $timezone): string {
        if (self::okay($timezone)) {
            return self::$descriptions[$timezone];
        } else {
            return "";
        }
    }

    public static function getDescriptions(): array {
        return self::$descriptions;
    }

    public static function getUnixOffset(int $timezone): int {
        return $timezone * 3600;
    }
}