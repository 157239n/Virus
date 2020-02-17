<?php /** @noinspection PhpIncludeInspection */

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\PackageRegistrar;
use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;
use Kelvinho\Virus\Singleton\Logs;
use Kelvinho\Virus\Singleton\Timezone;
use function Kelvinho\Virus\formattedTime;

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

if (!$session->has("virus_id")) Header::redirectToHome();
if (!$session->has("attack_id")) Header::redirectToHome();

$virus_id = $session->getCheck("virus_id");
$attack_id = $session->getCheck("attack_id");

if (!$authenticator->authorized($virus_id, $attack_id)) Header::redirectToHome();

$attack = $attackFactory->get($attack_id);

$packageDirectory = $packageRegistrar->getLocation($attack->getPackageDbName());
if (!$session->has("attack_id")) Header::redirectToHome();

$user = $userFactory->get($session->get("user_handle")); ?>
<html lang="en_US">
<head>
    <title>Attack info</title>
    <?php echo HtmlTemplate::header(); ?>
    <?php @include($packageDirectory . "/ui/styles.php"); ?>
</head>
<body>

<h1><a href="<?php echo DOMAIN_VIRUS_INFO; ?>">Attack info</a></h1>
<br>
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
        if ($attack->getStatus() === AttackBase::STATUS_EXECUTED) echo ", " . formattedTime($attack->getExecutedTime() + Timezone::getUnixOffset($user->getTimezone())) . " UTC " . $user->getTimezone(); ?>">
    </div>
</div>
<br>
<label for="package_description">Package description</label>
<textarea id="package_description" class="w3-input"
          disabled><?php echo $packageRegistrar->getDescription($attack->getPackageDbName()); ?></textarea>
<br>
<label for="profile">Profile</label>
<textarea id="profile" class="w3-input"><?php echo $attack->getProfile(); ?></textarea>
<br>
<?php @include($packageDirectory . "/ui/fields.php"); ?>
<div class="w3-button w3-red" onclick="update()">Update</div>
<?php
switch ($attack->getStatus()) {
    case AttackBase::STATUS_DORMANT: ?>
        <p>This attack is dormant. Click <a onclick="deployAttack()" class="link">here</a> to deploy.</p>
        <?php @include($packageDirectory . "/ui/message_dormant.php");
        break;
    case AttackBase::STATUS_DEPLOYED: ?>
        <p>This attack is deployed. Click <a onclick="cancelAttack()" class="link">here</a> to cancel the
            attack.</p>
        <?php @include($packageDirectory . "/ui/message_deployed.php");
        break;
    case AttackBase::STATUS_EXECUTED:
        @include $packageDirectory . "/ui/message_executed.php";
        break;
    default:
        Logs::error("Attack status of " . $attack->getStatus() . " is not defined. This really should not happen at all and please dig into it immediately.");
}
@include($packageDirectory . "/ui/footnote.php"); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="https://157239n.com/page/assets/js/main.js"></script>
<script type="application/javascript">
    function update() {
        $.ajax({
            url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/update"; ?>",
            type: "POST",
            data: {
                name: $("#name").val(),
                profile: $("#profile").val()
                <?php @include($packageDirectory . "/ui/fields_js.php"); ?>
            },
            success: function (response) {
                console.log(response);
                window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>"
            }
        });
    }

    function deployAttack() {
        $.ajax({
            url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/deploy"; ?>",
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
            url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/cancel"; ?>",
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

    // make the #profile textarea auto adjust the height
    $('#profile').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;resize:none;');
    }).on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    // make the #package_description textarea auto adjust the height
    $('#package_description').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;resize:none;');
    }).on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    <?php if ($attack->getStatus() === AttackBase::STATUS_DEPLOYED && $attack->getType() !== AttackBase::TYPE_BACKGROUND) { ?>
    // if results are ready, then refreshes the page
    setInterval(checkExecuted, 3000);

    function checkExecuted() {
        $.ajax({
            url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/executed"; ?>",
            type: "POST",
            success: function (response) {
                if (response === "1") window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>";
            }
        })
    }
    <?php } ?>
</script>
<?php @include($packageDirectory . "/ui/js.php"); ?>
</html>
