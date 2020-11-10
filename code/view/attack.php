<?php /** @noinspection PhpIncludeInspection */

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\PackageRegistrar;
use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;
use Kelvinho\Virus\Singleton\Logs;

global $session, $authenticator, $userFactory, $virusFactory, $attackFactory, $timezone, $packageRegistrar;

/** @var PackageRegistrar $packageRegistrar */

/**
 * Currently, these UI files can be defined (but not required to):
 * - fields.php: handle extra input fields that you might want to configure
 * - fields_js.php: should cite a bunch of field values defined in fields.php. Please see existing packages for examples
 * - footnote.php: footnote, is placed at the very last line of body. Can be used as a long explanation area
 * - js.php: extra javascript. This is placed inside the html tag, meaning you have to wrap this around script tag
 * - message_dormant: extra message when the attack is dormant
 * - message_deployed: extra message when the attack is deployed
 * - message_executed: extra message when the attack is executed
 * - styles.php: extra css. This is placed inside the head tag, meaning you have to wrap this around style tag
 */

if (!$session->hasAll(["virus_id", "attack_id", "user_handle"])) Header::redirectToHome();
if (!$authenticator->authorized($virus_id = $session->getCheck("virus_id"), $attack_id = $session->getCheck("attack_id"))) Header::redirectToHome();
$virus = $virusFactory->get($virus_id);
$attack = $attackFactory->get($attack_id);
$packageDirectory = $packageRegistrar->getLocation($attack->getPackageDbName());
$user = $userFactory->currentChecked(); ?>
<html lang="en_US">
<head>
    <title></title>
    <?php HtmlTemplate::header($user->isDarkMode()); ?>
    <?php @include($packageDirectory . "/ui/styles.php"); ?>
</head>
<body>
<?php HtmlTemplate::topNavigation($virus->getName(), $virus->getVirusId(), $attack->getAttackId(), $attackFactory);
HtmlTemplate::body(); ?>
<h2>Attack info</h2>
<div class="w3-row">
    <div class="w3-col l3 m4 s7">
        <label for="name">Name</label>
        <input id="name" class="w3-input" type="text" value="<?php echo $attack->getName(); ?>">
    </div>
    <div class="w3-col l9 m8 s5" style="padding-left: 8px">
        <label for="attack_id">Hash/id</label>
        <input id="attack_id" class="w3-input" type="text" disabled value="<?php echo $attack->getAttackId(); ?>">
    </div>
</div>
<br>
<div class="w3-row">
    <div class="w3-col l8 m7 s5">
        <label for="package">Package</label>
        <input id="package" class="w3-input" type="text" disabled
               value="<?php echo $packageRegistrar->getDisplayName($attack->getPackageDbName()); ?>">
    </div>
    <div class="w3-col l4 m5 s7" style="padding-left: 8px">
        <label for="status">Status</label>
        <input id="status" class="w3-input" type="text" disabled value="<?php echo $attack->getStatus();
        if ($attack->getStatus() === AttackBase::STATUS_EXECUTED) echo ", " . $timezone->display($user->getTimezone(), $attack->getExecutedTime()); ?>">
    </div>
</div>
<br>
<label for="package_description">Package description</label>
<textarea id="package_description" class="w3-input" style="resize: vertical"
          disabled><?php echo $packageRegistrar->getDescription($attack->getPackageDbName()); ?></textarea>
<br>
<label for="profile">Profile</label>
<textarea id="profile" class="w3-input" style="resize: vertical"><?php echo $attack->getProfile(); ?></textarea>
<br>
<?php @include($packageDirectory . "/ui/fields.php"); ?>
<button class="w3-btn w3-blue-grey" onclick="update()">Update</button>
<?php
switch ($attack->getStatus()) {
    case AttackBase::STATUS_DORMANT: ?>
        <button class="w3-btn w3-light-green" onclick="deployAttack()">Deploy</button>
        <?php @include($packageDirectory . "/ui/message_dormant.php");
        break;
    case AttackBase::STATUS_DEPLOYED: ?>
        <button class="w3-btn w3-light-green" onclick="cancelAttack()">Cancel</button>
        <?php @include($packageDirectory . "/ui/message_deployed.php"); ?>
        <p>Executing.... Please wait a moment</p>
        <?php break;
    case AttackBase::STATUS_EXECUTED:
        @include $packageDirectory . "/ui/message_executed.php";
        break;
    default:
        Logs::unreachableState("Attack status of " . $attack->getStatus() . " is not defined. This really should not happen at all and please dig into it immediately.");
}
@include($packageDirectory . "/ui/footnote.php"); ?>
<?php HtmlTemplate::scripts(); ?>
<script type="application/javascript">
    function update() {
        $.ajax({
            url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/update"; ?>",
            type: "POST", data: {
                name: $("#name").val(),
                profile: $("#profile").val()<?php @include($packageDirectory . "/ui/fields_js.php"); ?>
            },
            success: () => window.location = "<?php echo DOMAIN . "/ctrls/viewAttack?vrs=" . $attack->getVirusId() . "&aks=" . $attack->getAttackId(); ?>",
            error: () => toast.displayOfflineMessage("Can't update this attack.")
        });
    }

    function deployAttack() {
        $.ajax({
            url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/deploy"; ?>",
            type: "POST", data: {
                virus_id: "<?php echo $attack->getVirusId(); ?>", attack_id: "<?php echo $attack->getAttackId(); ?>"
            },
            success: () => window.location = "<?php echo DOMAIN . "/ctrls/viewAttack?vrs=" . $attack->getVirusId() . "&aks=" . $attack->getAttackId(); ?>",
            error: () => toast.displayOfflineMessage("Can't deploy attack.")
        });
    }

    function cancelAttack() {
        $.ajax({
            url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/cancel"; ?>",
            type: "POST", data: {
                virus_id: "<?php echo $attack->getVirusId(); ?>", attack_id: "<?php echo $attack->getAttackId(); ?>"
            },
            success: () => window.location = "<?php echo DOMAIN . "/ctrls/viewAttack?vrs=" . $attack->getVirusId() . "&aks=" . $attack->getAttackId(); ?>",
            error: () => toast.displayOfflineMessage("Can't cancel attack")
        })
    }

    autoAdjustHeight($('#profile'));
    autoAdjustHeight($('#package_description'));

    let title = "<?php echo $virus->getName(); ?> - Attack info";
    document.title = title;
    <?php if ($attack->getStatus() === AttackBase::STATUS_DEPLOYED && $attack->getType() !== AttackBase::TYPE_BACKGROUND) { ?>
    // if results are ready, then refreshes the page
    setInterval(checkExecuted, 500);

    function checkExecuted() {
        $.ajax({
            url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/executed"; ?>",
            type: "POST",
            success: response => response === "1" ? window.location = "<?php echo DOMAIN . "/ctrls/viewAttack?vrs=" . $attack->getVirusId() . "&aks=" . $attack->getAttackId(); ?>" : 0
        })
    }
    <?php }
    // these are for blinking the title if the user has not focused on the screen, as a way to notify them
    if ($attack->getStatus() === AttackBase::STATUS_EXECUTED) { ?>
    let state = 0;
    blinkTitleInterval = setInterval(() => document.title = ((state = 1 - state) === 0 ? "(Executed) " : " ") + title, 2000);

    $(document).on("mousemove", function () {
        clearInterval(blinkTitleInterval);
        document.title = title;
        $(document).off();
    });
    <?php }
    ?>
</script>
<?php @include($packageDirectory . "/ui/js.php"); ?>
</html>
