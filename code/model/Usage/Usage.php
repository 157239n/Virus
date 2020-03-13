<?php

namespace Kelvinho\Virus\Usage;

use Kelvinho\Virus\Singleton\Logs;
use mysqli;
use function Kelvinho\Virus\niceCost;
use function Kelvinho\Virus\niceFileSize;

/**
 * Class Usage. Represent resource usage so that it can be billed.
 *
 * @package Kelvinho\Virus\Usage
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Usage {
    public const MONTHLY_QUOTA = 1000; // $10
    public const MAX_UNPAID_AMOUNT = 1000; // $10
    private mysqli $mysqli;
    private int $usage_id;
    private int $static_disk = 0; // in bytes
    private int $static_bandwidth = 0; // in bytes
    private int $dynamic_api_geolocation = 0; // in polls
    private int $last_updated_time;
    private float $static_disk_cents_per_byte = 10e-7; // $0.1/GB each month
    private float $static_bandwidth_cents_per_byte = 5 * 10e-9; // $5/TB each month
    private float $dynamic_api_geolocation_cents_per_request = 0.5; // good for 2 targets

    public function __construct(mysqli $mysqli, int $usage_id) {
        $this->mysqli = $mysqli;
        $this->usage_id = $usage_id;
        $this->loadState();
    }

    private function loadState(): void {
        if (!$answer = $this->mysqli->query("select * from resource_usage where id = $this->usage_id")) throw new UsageNotFound();
        if (!$row = $answer->fetch_assoc()) throw new UsageNotFound();
        $this->static_disk = $row["static_disk"];
        $this->static_bandwidth = $row["static_bandwidth"];
        $this->dynamic_api_geolocation = $row["dynamic_api_geolocation"];
        $this->last_updated_time = $row["last_updated_time"];
    }

    public function saveState(): void {
        if (!$this->mysqli->query("update resource_usage set last_updated_time = $this->last_updated_time, static_disk = $this->static_disk, static_bandwidth = $this->static_bandwidth, dynamic_api_geolocation = $this->dynamic_api_geolocation where id = $this->usage_id")) Logs::mysql($this->mysqli);
    }

    public function getId(): int {
        return $this->usage_id;
    }

    public function resetDynamic(int $unixTime = 0): Usage {
        if ($unixTime == 0) $unixTime = time();
        $this->last_updated_time = $unixTime;
        // set all dynamic ones to 0
        $this->dynamic_api_geolocation = 0;
        return $this;
    }

    public function add(Usage $usage): void {
        $this->static_disk += $usage->static_disk;
        $this->static_bandwidth += $usage->static_bandwidth;
        $this->addDynamic($usage);
    }

    public function addDynamic(Usage $usage): void {
        $this->dynamic_api_geolocation += $usage->dynamic_api_geolocation;
    }

    public function display(): void {
        $static_disk_cents = $this->static_disk * $this->static_disk_cents_per_byte;
        $static_bandwidth_cents = $this->static_bandwidth * $this->static_bandwidth_cents_per_byte;
        $dynamic_api_geolocation_cents = $this->dynamic_api_geolocation * $this->dynamic_api_geolocation_cents_per_request;
        ?>
        <table class="w3-table w3-bordered w3-border w3-hoverable w3-card-2">
            <tr class="w3-white">
                <th>Resource</th>
                <th>Amount</th>
            </tr>
            <tr>
                <td>Disk space</td>
                <td><?php echo niceFileSize($this->static_disk); ?>, $<?php echo niceCost($static_disk_cents); ?></td>
            </tr>
            <tr>
                <td>Bandwidth</td>
                <td><?php echo niceFileSize($this->static_bandwidth); ?>,
                    $<?php echo niceCost($static_bandwidth_cents); ?></td>
            </tr>
            <tr>
                <td>Geolocation API</td>
                <td><?php echo $this->dynamic_api_geolocation; ?> requests,
                    $<?php echo niceCost($dynamic_api_geolocation_cents); ?></td>
            </tr>
            <tr class="w3-light-grey">
                <td>Total</td>
                <td>
                    $<?php echo niceCost($static_disk_cents + $static_bandwidth_cents + $dynamic_api_geolocation_cents) ?></td>
            </tr>
        </table>
    <?php }

    public function getMoney(): int {
        return $this->static_disk * $this->static_disk_cents_per_byte +
            $this->static_bandwidth * $this->static_bandwidth_cents_per_byte +
            $this->dynamic_api_geolocation * $this->dynamic_api_geolocation_cents_per_request;
    }

    public function allowed(int $unpaidAmount): bool {
        $totalUsage = $this->getMoney();
        return $totalUsage > self::MONTHLY_QUOTA and ($unpaidAmount > self::MAX_UNPAID_AMOUNT or $totalUsage - self::MONTHLY_QUOTA > self::MAX_UNPAID_AMOUNT);
    }

    public function getLastUpdated(): int {
        return $this->last_updated_time;
    }

    public function setLastUpdated(int $unixTime): void {
        $this->last_updated_time = $unixTime;
    }

    public function getDisk(): int {
        return $this->static_disk;
    }

    public function setDisk(int $diskInBytes): void {
        $this->static_disk = $diskInBytes;
    }

    public function getBandwidth(): int {
        return $this->static_bandwidth;
    }

    public function setBandwidth(int $bandwidthInBytes): void {
        $this->static_bandwidth = $bandwidthInBytes;
    }

    public function getApi(): int {
        return $this->dynamic_api_geolocation;
    }

    public function getApiGeolocation(): int {
        return $this->dynamic_api_geolocation;
    }

    public function setApiGeolocation(int $priceInCents): void {
        $this->dynamic_api_geolocation = $priceInCents;
    }
}
