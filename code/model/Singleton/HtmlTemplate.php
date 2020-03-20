<?php

namespace Kelvinho\Virus\Singleton;

use Kelvinho\Virus\Attack\AttackFactory;

/**
 * Class HtmlTemplate, provides sort of a shared structure. This is not really elegant, I will factor it later.
 *
 * @package Kelvinho\Virus\Singleton
 */
class HtmlTemplate {
    /**
     * The header (no <head> included. This contains:
     * - Css from 157239n.com
     * - Css from w3school.com
     * - Viewports
     * - .link classes have pointer cursor and their color blue
     */
    public static function header(): void { ?>
        <!-- <link rel="stylesheet" type="text/css" href="https://resource.kelvinho.org/assets/css/main.css"> -->
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css?family=Inconsolata|Droid+Sans|Lato|PT+Sans|Open+Sans">
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <!--suppress CssUnusedSymbol -->
        <style>
            .link {
                cursor: pointer;
                color: blue;
            }

            .w3-table td {
                vertical-align: inherit;
            }

            body {
                padding: 0 7% 6vh;
                text-align: justify;
                font-family: "Open Sans", serif;
                line-height: 1.5;
            }

            h1 {
                color: rgb(69, 128, 100);
            }

            h2 {
                color: #616161;
            }

            a {
                text-decoration: none;
                /*color: rgb(20, 131, 168);*/
            }
        </style>
        <script>
            function topNavDeleteAttack(virus_id, attack_id, redirect_attack_id) {
                $.ajax({
                    url: "<?php echo DOMAIN; ?>/vrs/" + virus_id + "/aks/" + attack_id + "/ctrls/delete",
                    type: "POST",
                    success: () => window.location = "<?php echo DOMAIN . "/ctrls/viewAttack?vrs=" ?>" + virus_id + "<?php echo "&aks="; ?>" + redirect_attack_id
                })
            }

            function topNavToggleHold(isHolding) {
                if (isHolding) {
                    $.ajax({
                        url: "<?php echo DOMAIN; ?>/ctrls/removeHold",
                        type: "POST",
                        success: () => window.location = "<?php echo DOMAIN ?>"
                    })
                } else {
                    $.ajax({
                        url: "<?php echo DOMAIN; ?>/ctrls/applyHold",
                        type: "POST",
                        success: () => window.location = "<?php echo DOMAIN ?>"
                    })
                }
            }
        </script>
    <?php }

    public static function topNavigation(string $virusName = null, string $virus_id = null, string $attack_id = null, AttackFactory $attackFactory = null, bool $holding = null): void { ?>
        <div class="w3-bar w3-light-grey w3-card" style="position: fixed;left: 0;top: 0;z-index: 200;">
            <a href="<?php echo DOMAIN; ?>" class="w3-bar-item w3-button">Dashboard</a>
            <?php if ($virusName !== null) { ?>
                <a href="<?php echo DOMAIN . "/ctrls/viewVirus?vrs=$virus_id"; ?>"
                   class="w3-bar-item w3-button w3-border-left"><span class="w3-hide-small">Virus: </span><?php echo $virusName; ?></a>
            <?php }
            if ($attack_id !== null) {
                $attack = $attackFactory->get($attack_id);
                $around = $attack->getAround();
                $redirect_attack_id = $around[0] !== null ? $around[0] : ($around[1] !== null ? $around[1] : ""); ?>
                <div class="w3-bar-item w3-hide-small" style="padding: 0;margin-left: 16%">
                    <?php if ($around[0] !== null) { ?>
                        <a href="<?php echo DOMAIN . "/ctrls/viewAttack?vrs=$virus_id&aks=" . $around[0]; ?>"
                           class="w3-button" style="height: 38px;"><i class="material-icons">keyboard_arrow_left</i></a>
                    <?php } ?>
                    <a onclick="topNavDeleteAttack('<?php echo $virus_id; ?>', '<?php echo $attack_id; ?>', '<?php echo $redirect_attack_id; ?>')"
                       class="w3-button" style="height: 38px;"><i class="material-icons">delete</i></a>
                    <?php if ($around[1] !== null) { ?>
                        <a href="<?php echo DOMAIN . "/ctrls/viewAttack?vrs=$virus_id&aks=" . $around[1]; ?>"
                           class="w3-button" style="height: 38px;"><i
                                    class="material-icons">keyboard_arrow_right</i></a>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="w3-bar-item w3-button w3-right w3-dropdown-hover" style="height: 38px;"><i class="material-icons">settings</i>
                <div class="w3-dropdown-content w3-bar-block w3-card-4" style="position: fixed;right: 0; top: 38px;">
                    <a href="<?php echo DOMAIN . "/profile"; ?>" class="w3-bar-item w3-button">Profile</a>
                    <a href="<?php echo DOMAIN . "/logout"; ?>" class="w3-bar-item w3-button">Sign out</a>
                </div>
            </div>
            <?php
            if ($holding !== null) { ?>
                <a onclick="<?php echo $holding ? "topNavToggleHold(true)" : "topNavToggleHold(false)"; ?>"
                   class="w3-bar-item w3-button w3-right w3-border-right <?php echo $holding ? "w3-red" : ""; ?> <?php echo $virus_id !== null ? "w3-hide-small" : ""; ?>">Hold</a>
            <?php } ?>
        </div>
        <br><br>
    <?php }

    /**
     * The scripts tag. This contains:
     * - jquery minified cdn
     * - javascript from 157239n.com
     */
    public static function scripts(): void { ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <?php }
}
