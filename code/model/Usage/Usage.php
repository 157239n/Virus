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
    private int $dynamic_bandwidth = 0; // in bytes
    private int $dynamic_api_geolocation = 0; // in polls
    private float $static_disk_cents_per_byte = 1e-6; // $10/GB each month
    private float $dynamic_bandwidth_cents_per_byte = 5e-9; // $5/TB each month
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
        $this->dynamic_bandwidth = $row["dynamic_bandwidth"];
        $this->dynamic_api_geolocation = $row["dynamic_api_geolocation"];
    }

    public function saveState(): void {
        if (!$this->mysqli->query("update resource_usage set static_disk = $this->static_disk, dynamic_bandwidth = $this->dynamic_bandwidth, dynamic_api_geolocation = $this->dynamic_api_geolocation where id = $this->usage_id")) Logs::mysql($this->mysqli);
    }

    public function getId(): int {
        return $this->usage_id;
    }

    public function resetDynamic(): Usage {
        $this->dynamic_bandwidth = 0;
        $this->dynamic_api_geolocation = 0;
        return $this;
    }

    public function addStatic(Usage $usage): Usage {
        $this->static_disk += $usage->static_disk;
        return $this;
    }

    public function addDynamic(Usage $usage): Usage {
        $this->dynamic_api_geolocation += $usage->dynamic_api_geolocation;
        $this->dynamic_bandwidth += $usage->dynamic_bandwidth;
        return $this;
    }

    public function minusStatic(Usage $usage): Usage {
        $this->static_disk -= $usage->static_disk;
        return $this;
    }

    public function display(): void {
        $static_disk_cents = $this->static_disk * $this->static_disk_cents_per_byte;
        $dynamic_bandwidth_cents = $this->dynamic_bandwidth * $this->dynamic_bandwidth_cents_per_byte;
        $dynamic_api_geolocation_cents = $this->dynamic_api_geolocation * $this->dynamic_api_geolocation_cents_per_request;
        ?>
        <table class="w3-table w3-bordered w3-border w3-hoverable w3-card-2">
            <tr class="w3-white table-heads">
                <th>Resource</th>
                <th>Amount</th>
            </tr>
            <tr>
                <td>Disk space</td>
                <td><?php echo niceFileSize($this->static_disk); ?>, $<?php echo niceCost($static_disk_cents); ?></td>
            </tr>
            <tr>
                <td>Bandwidth</td>
                <td><?php echo niceFileSize($this->dynamic_bandwidth); ?>,
                    $<?php echo niceCost($dynamic_bandwidth_cents); ?></td>
            </tr>
            <tr>
                <td>Geolocation API</td>
                <td><?php echo $this->dynamic_api_geolocation; ?> requests,
                    $<?php echo niceCost($dynamic_api_geolocation_cents); ?></td>
            </tr>
            <tr class="w3-dark-grey">
                <td>Total</td>
                <td>
                    $<?php echo niceCost($static_disk_cents + $dynamic_bandwidth_cents + $dynamic_api_geolocation_cents) ?></td>
            </tr>
        </table>
    <?php }

    public function getMoney(): int {
        return $this->static_disk * $this->static_disk_cents_per_byte +
            $this->dynamic_bandwidth * $this->dynamic_bandwidth_cents_per_byte +
            $this->dynamic_api_geolocation * $this->dynamic_api_geolocation_cents_per_request;
    }

    public function allowed(int $unpaidAmount): bool {
        $totalUsage = $this->getMoney();
        return $totalUsage > self::MONTHLY_QUOTA and ($unpaidAmount > self::MAX_UNPAID_AMOUNT or $totalUsage - self::MONTHLY_QUOTA > self::MAX_UNPAID_AMOUNT);
    }

    public function setDisk(int $diskInBytes): Usage {
        $this->static_disk = $diskInBytes;
        return $this;
    }

    public function addDisk(int $diskInBytes): Usage {
        $this->static_disk += $diskInBytes;
        return $this;
    }

    public function minusDisk(int $diskInBytes): Usage {
        $this->static_disk -= $diskInBytes;
        return $this;
    }

    public function addBandwidth(int $bandwidthInBytes): Usage {
        $this->dynamic_bandwidth += $bandwidthInBytes;
        return $this;
    }

    public function getStatic(): int {
        return $this->static_disk;
    }

    public function incApiGeolocation(): Usage {
        $this->dynamic_api_geolocation++;
        return $this;
    }
}
