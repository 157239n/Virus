<?php

use Kelvinho\Virus\Attack\AdminTemplates;
use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Header;
use function Kelvinho\Virus\logAttackStatus;
use function Kelvinho\Virus\map;

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
            <p>This attack is executed. The environmental variables are:</p>
            <ul style = "overflow: auto;">
                <?php map($attack->getEnv(), function ($values, $key) { ?>
                    <li>
                        <pre><?php echo $key; ?></pre>
                        <ul>
                            <?php map($values, function ($value) { ?>
                                <li>
                                    <pre><?php echo $value; ?></pre>
                                </li>
                            <?php }); ?>
                        </ul>
                    </li>
                <?php }); ?>
            </ul>
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
                    profile: $("#profile").val()
                },
                success: function (response) {
                    console.log(response);
                    window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>"
                }
            });
        }
    </script>
    <?php echo AdminTemplates::script($attack); ?>
    </html>
<?php } ?>