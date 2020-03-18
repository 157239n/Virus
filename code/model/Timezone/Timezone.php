<?php

namespace Kelvinho\Virus\Timezone;

use DateTime;
use DateTimeZone;

/**
 * Class Timezone, handles timezones.
 *
 * @package Kelvinho\Virus\Singleton
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Timezone {
    /** @var String[] */
    private array $timezones = [];
    private array $niceLookingTimezones = []; // associative array of timezone string => nice looking name
    private array $offsetsInSeconds = []; // associative array of timezone string => offset in seconds

    public function __construct() {
        $this->timezones = DateTimeZone::listIdentifiers();
        $now = new DateTime();
        foreach ($this->timezones as $timezoneString) {
            $timezone = new DateTimeZone($timezoneString);
            $offset = $timezone->getOffset($now);
            $this->niceLookingTimezones[$timezoneString] = "(UTC" . ($offset >= 0 ? "+" : "-") . gmdate('H:i', abs($offset)) . ") $timezoneString";
            $this->offsetsInSeconds[$timezoneString] = $offset;
        }
        usort($this->timezones, fn($x, $y) => $this->offsetsInSeconds[$x] - $this->offsetsInSeconds[$y]);
    }

    public function getTimezones(): array {
        return $this->timezones;
    }

    public function hasTimezone(string $timezone): bool {
        return isset($this->niceLookingTimezones[$timezone]);
    }

    public function getDescription(string $timezone): string {
        return $this->niceLookingTimezones[$timezone];
    }

    public function getOffset(string $timezone): int {
        return $this->offsetsInSeconds[$timezone];
    }

    public function display(string $timezone, int $unixTime): string {
        return (new DateTime())
            ->setTimestamp($unixTime)
            ->setTimezone(new DateTimeZone($timezone))
            ->format("Y/m/d h:i:sa");
    }
}