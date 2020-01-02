<?php

use Kelvinho\Virus\Attack\AdminTemplates;
use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\AttackPackages\Windows\OneTime\CheckPermission;
use Kelvinho\Virus\Header;
use function Kelvinho\Virus\logAttackStatus;
use function Kelvinho\Virus\logError;
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
    <div>Directories</div>
    <textarea id="directories" rows="12" cols="80" class="w3-input"
              style="resize: vertical;"
        <?php if ($attack->getStatus() != AttackInterface::STATUS_DORMANT) {
            echo "disabled";
        }; ?>
    ><?php echo $attack->getDirectoriesAsBlock(); ?></textarea>
    <?php
    switch ($attack->getStatus()) {
        case AttackInterface::STATUS_DORMANT: ?>
            <p>Type in the directories you want to check the permission of above. Remember that you must place absolute
                paths, or there will be an error.</p>
            <?php break;
        case AttackInterface::STATUS_DEPLOYED: ?>
            <p>If you wish to edit the directories, change it to dormant mode below.</p>
            <?php break;
        case AttackInterface::STATUS_EXECUTED:
            break;
        default:
            logError("Attack status of " . $attack->getStatus() . " does not exist");
    }
    ?>
    <br>
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
            <p>This attack is executed. Allowed directories:</p>
            <ul>
                <?php
                $directories = $attack->getDirectories(CheckPermission::$PERMISSION_ALLOWED);
                if (count($directories) == 0) { ?>
                    (No directories)
                <?php } else {
                    map($directories, function ($directory) { ?>
                        <li>
                            <pre><?php echo $directory["path"]; ?></pre>
                        </li>
                    <?php });
                } ?>
            </ul>
            <p>Not allowed directories:</p>
            <ul>
                <?php
                $directories = $attack->getDirectories(CheckPermission::$PERMISSION_NOT_ALLOWED);
                if (count($directories) == 0) { ?>
                    (No directories)
                <?php } else {
                    map($directories, function ($directory) { ?>
                        <li>
                            <pre><?php echo $directory["path"]; ?></pre>
                        </li>
                    <?php });
                } ?>
            </ul>
            <p>Directories that does not exist:</p>
            <ul>
                <?php
                $directories = $attack->getDirectories(CheckPermission::$PERMISSION_DOES_NOT_EXIST);
                if (count($directories) == 0) { ?>
                    (No directories)
                <?php } else {
                    map($directories, function ($directory) { ?>
                        <li>
                            <pre><?php echo $directory["path"]; ?></pre>
                        </li>
                    <?php });
                } ?>
            </ul>
            <p>Something has gone wrong with these directories:</p>
            <ul>
                <?php
                $directories = $attack->getDirectories(CheckPermission::$PERMISSION_UNSET);
                if (count($directories) == 0) { ?>
                    (No directories)
                <?php } else {
                    map($directories, function ($directory) { ?>
                        <li>
                            <pre><?php echo $directory["path"]; ?></pre>
                        </li>
                    <?php });
                } ?>
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
                    <?php if ($attack->getStatus() == AttackInterface::STATUS_DORMANT) { ?>
                    , directories: $("#directories").val()
                    <?php } ?>
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