<?php

namespace Kelvinho\Virus\Usage;

use Kelvinho\Virus\Singleton\Logs;

/**
 * Class UsageFactory
 *
 * @package Kelvinho\Virus\Usage
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class UsageFactory {
    private \mysqli $mysqli;

    public function __construct(\mysqli $mysqli) {
        $this->mysqli = $mysqli;
    }

    public function get(int $usage_id): Usage {
        return new Usage($this->mysqli, $usage_id);
    }

    public function new(): Usage {
        if (!$this->mysqli->query("insert into resource_usage (last_updated_time, static_disk, static_bandwidth, dynamic_api_geolocation) values (0, 0, 0, 0)")) Logs::mysql($this->mysqli);
        return $this->get($this->mysqli->insert_id);
    }
}
