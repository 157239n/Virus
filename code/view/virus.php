<?php

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\AttackFactory;
use Kelvinho\Virus\Attack\PackageRegistrar;
use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;
use Kelvinho\Virus\Singleton\Torch;
use Kelvinho\Virus\Timezone\Timezone;
use Kelvinho\Virus\User\User;
use function Kelvinho\Virus\filter;
use function Kelvinho\Virus\formattedHash;
use function Kelvinho\Virus\map;
use function Kelvinho\Virus\niceFileSize;

global $mysqli, $timezone, $session, $userFactory, $virusFactory, $attackFactory, $authenticator, $demos, $packageRegistrar;

/** @var PackageRegistrar $packageRegistrar */

/**
 * Displays a table of viruses.
 *
 * @param array $attack_ids A list of attack_ids
 * @param array $visibleFields An array containing the columns you want to display. Example would be [0, 1, 2, 4]
 * @param AttackFactory $attackFactory
 * @param User $user
 * @param PackageRegistrar $packageRegistrar
 * @param Timezone $timezoneObject
 */
function displayTable(array $attack_ids, array $visibleFields, AttackFactory $attackFactory, User $user, PackageRegistrar $packageRegistrar, Timezone $timezoneObject) {
    if (count($attack_ids) === 0) echo "<p>(No attacks)</p>";
    else { ?>
        <div style="overflow: auto;" class="w3-card table-round">
            <table class="w3-table w3-bordered w3-hoverable">
                <tr class="w3-white table-heads"><?php
                    echo in_array(0, $visibleFields) ? "<th>Name</th>" : "";
                    echo in_array(1, $visibleFields) ? "<th>Package</th>" : "";
                    echo in_array(2, $visibleFields) ? "<th>Hash/id</th>" : "";
                    echo in_array(3, $visibleFields) ? "<th>Executed time</th>" : "";
                    echo in_array(4, $visibleFields) ? "<th>Disk space</th>" : "";
                    echo in_array(5, $visibleFields) ? "<th></th>" : ""; ?>
                </tr>
                <?php foreach ($attack_ids as $attack_id) {
                    $attack = $attackFactory->get($attack_id);
                    echo "<tr onclick = \"redirect(event, '$attack_id')\" style=\"cursor: pointer;\">";
                    echo in_array(0, $visibleFields) ? "<td>" . $attack->getName() . "</td>" : "";
                    echo in_array(1, $visibleFields) ? "<td>" . $packageRegistrar->getDisplayName($attack->getPackageDbName()) . "</td>" : "";
                    echo in_array(2, $visibleFields) ? "<td>" . formattedHash($attack->getAttackId()) . "</td>" : "";
                    echo in_array(3, $visibleFields) ? "<td>" . $timezoneObject->display($user->getTimezone(), $attack->getExecutedTime()) . "</td>" : "";
                    echo in_array(4, $visibleFields) ? "<td>" . niceFileSize($attack->usage()->getStatic()) . "</td>" : "";
                    echo in_array(5, $visibleFields) ? "<td class='w3-right-align'><button class=\"w3-btn w3-teal\" onclick=\"deleteAttack('" . $attack->getAttackId() . "')\">Delete</button></td>" : "";
                    echo "</a></tr>";
                } ?>
            </table>
        </div>
    <?php }
}

if (!$session->has("virus_id")) Header::redirectToHome();
if (!$authenticator->authorized($virus_id = $session->getCheck("virus_id"))) Header::redirectToHome();

$virus = $virusFactory->get($virus_id);
$user = $userFactory->currentChecked(); ?>
<html lang="en_US">
<head>
    <title><?php echo $virus->getName(); ?> - Virs</title>
    <?php HtmlTemplate::header($user->isDarkMode()); ?>
    <style>
        .w3-table td {
            vertical-align: inherit;
        }

        .transparentNav {
            transition: var(--smooth) opacity;
            position: absolute;
            width: 30%;
            height: 100%;
            opacity: 0;
        }

        .transparentNav:hover {
            opacity: var(--translucent);
            cursor: pointer;
        }

        .chartDiv {
            position: relative;
            margin: auto;
            width: 49%;
            display: inline-block;
        }

        @media only screen and (max-width: 900px) {
            .chartDiv {
                width: 80%;
                display: block;
            }
        }

        @media only screen and (max-width: 600px) {
            .chartDiv {
                width: 100%;
                display: block;
            }
        }
    </style>
</head>
<body>
<?php HtmlTemplate::topNavigation($virus->getName(), $virus->getVirusId(), null, null, $user->isHold());
HtmlTemplate::body(); ?>
<h2>Virus info</h2>
<div class="w3-row">
    <div class="w3-col l4 m4 s6" style="padding-right: 8px;">
        <label for="name">Name</label>
        <input id="name" class="w3-input" type="text" value="<?php echo $virus->getName(); ?>">
    </div>
    <div class="w3-col l4 m4 w3-hide-small" style="padding-right: 8px;">
        <label for="hash">Hash/id</label>
        <input id="hash" class="w3-input" type="text" disabled value="<?php echo $virus_id; ?>">
    </div>
    <div class="w3-col l4 m4 s6">
        <label for="lastPing">Last seen</label>
        <input id="lastPing" class="w3-input" type="text" disabled
               value="<?php echo $timezone->display($user->getTimezone(), $virus->getLastPing()) . " (Unix timestamp: " . $virus->getLastPing() . ")"; ?>">
    </div>
</div>
<br>
<label for="profile">Profile</label>
<textarea id="profile" cols="80" class="w3-input"><?php echo $virus->getProfile(); ?></textarea>
<br>
<button class="w3-btn w3-red" onclick="updateVirus()">Update</button>
<h2>Activity</h2>
<div style="background-color: var(--surface)" class="w3-card w3-round">
    <div style="text-align: center">
        <div id="dailyChartDiv" class="chartDiv">
            <div style="left: 0; border-radius: 50px 0 0 50px" class="w3-grey transparentNav"
                 onclick="changeDayGraph(false)"></div>
            <div style="left: 70%; border-radius: 0 50px 50px 0" class="w3-grey transparentNav"
                 onclick="changeDayGraph(true)"></div>
            <canvas id="dailyChart"></canvas>
        </div>
        <div id="monthlyChartDiv" class="chartDiv">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
</div>
<p>The daily active view shows at which times the virus reports back indicating that it is still alive and still
    listening, while the monthly frequency view shows how many hours the virus is active in a particular hour (ex.
    from 03:00 to 04:00) for a whole month. The two actually is kinda similar, not much different there.</p>
<h2>New attack</h2>
<label for="attackName">Name. This is an optional short name to help you stay organized</label>
<input id="attackName" class="w3-input" type="text">
<br>
<div class="w3-row">
    <!--suppress HtmlFormInputWithoutLabel -->
    <select id="attackPackage" class="w3-select w3-col l10 m9 s8" name="option" style="padding: 8px;">
        <option value="" disabled selected>Choose attack package</option>
        <?php echo implode(map($packageRegistrar->getPackages(), fn($dbName) => "<option value='$dbName'>" . $packageRegistrar->getDisplayName($dbName) . "</option>")); ?>
    </select>
    <div style="padding-left: 8px;" class="w3-col l2 m3 s4">
        <button class="w3-btn w3-block w3-red" onclick="continueAttack()">Continue</button>
    </div>
</div>
<?php echo implode(map($packageRegistrar->getPackages(), fn($dbName) => "<p id='packageDescription-$dbName' class='packageDescriptions' style='display: none'>Package description: " . $packageRegistrar->getDescription($dbName) . "</p>")); ?>
<div id="message" style="color: red;"></div>
<h2>Background attacks</h2>
<h3>Offline</h3>
<?php displayTable($virus->getAttacks(AttackBase::STATUS_DORMANT, null, [AttackBase::TYPE_BACKGROUND]), [0, 1, 2, 4, 5], $attackFactory, $user, $packageRegistrar, $timezone); ?>
<h3>Online</h3>
<?php displayTable($virus->getAttacks(AttackBase::STATUS_DEPLOYED, null, [AttackBase::TYPE_BACKGROUND]), [0, 1, 2, 4, 5], $attackFactory, $user, $packageRegistrar, $timezone); ?>
<h2>One time attacks</h2>
<h3>Dormant attacks</h3>
<?php displayTable($virus->getAttacks(AttackBase::STATUS_DORMANT, null, [AttackBase::TYPE_ONE_TIME, AttackBase::TYPE_SESSION]), [0, 1, 2, 4, 5], $attackFactory, $user, $packageRegistrar, $timezone); ?>
<h3>Deployed attacks</h3>
<?php displayTable($virus->getAttacks(AttackBase::STATUS_DEPLOYED, null, [AttackBase::TYPE_ONE_TIME, AttackBase::TYPE_SESSION]), [0, 1, 2, 4, 5], $attackFactory, $user, $packageRegistrar, $timezone); ?>
<h3>Executed attacks</h3>
<?php displayTable($virus->getAttacks(AttackBase::STATUS_EXECUTED, null, [AttackBase::TYPE_ONE_TIME, AttackBase::TYPE_SESSION]), [0, 1, 2, 3, 4, 5], $attackFactory, $user, $packageRegistrar, $timezone); ?>
<h2>How to attack?</h2>
<?php $demos->renderVirusHowTo(); ?>
<h2>Which package?</h2>
<?php $demos->renderVirusWhich(); ?>
</body>
<?php HtmlTemplate::scripts(); ?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
<script type="application/javascript">
    const gui = {
        packageDescriptions: $(".packageDescriptions"), message: $("#message"),
        attackName: $("#attackName"), attackPackage: $("#attackPackage")
    };

    function updateVirus() {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/updateVirus", type: "POST",
            data: {virus_id: "<?php echo $virus_id; ?>", name: $("#name").val(), profile: $("#profile").val()},
            success: () => window.location = "<?php echo DOMAIN . "/ctrls/viewVirus?vrs=$virus_id"; ?>",
            error: () => toast.displayOfflineMessage("Can't update virus.")
        });
    }

    gui.attackPackage.change(function () {
        const packageDbName = gui.attackPackage.val();
        gui.packageDescriptions.css("display", "none");
        $("#packageDescription-" + packageDbName.replace(/\./g, "\\.")).css("display", "block");
        gui.message.html("");
    });

    function continueAttack() {
        if (!gui.attackPackage.val()) return toast.display("Please choose an attack package first");
        let attackName = gui.attackName.val();
        if (attackName.length > 50) return toast.display("Message name should be less than 50 characters");
        if (attackName === "") attackName = "(not set)";
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER . "/newAttack"; ?>", type: "POST",
            data: {virus_id: <?php echo "\"$virus_id\""; ?>, attack_package: gui.attackPackage.val(), name: attackName},
            success: () => window.location = "<?php echo DOMAIN . "/attack"; ?>",
            error: () => toast.displayOfflineMessage("Can't create a new attack.")
        });
    }

    let clickHold = false; // prevent the delete button click from triggering the tr click

    function deleteAttack(attack_id) {
        clickHold = true;
        $.ajax({
            url: "<?php echo DOMAIN; ?>/vrs/<?php echo $virus->getVirusId(); ?>/aks/" + attack_id + "/ctrls/delete",
            type: "POST",
            success: () => window.location = "<?php echo DOMAIN . "/ctrls/viewVirus?vrs=$virus_id"; ?>",
            error: () => toast.displayOfflineMessage("Can't delete attack.")
        });
    }

    function redirect(event, attack_id) {
        if (clickHold) return clickHold = false;
        if (event.ctrlKey) window.open("<?php echo DOMAIN . "/ctrls/viewAttack?vrs=$virus_id&aks="; ?>" + attack_id, "_blank");
        else window.location = "<?php echo DOMAIN . "/ctrls/viewAttack?vrs=$virus_id&aks="; ?>" + attack_id;
    }

    <?php

    function fixHour(int $hour) {
        return ($hour + 24 * 3) % 24;
    }

    $uptimes = []; // [{"unix_time" => ..., "active" => 0}, ...]
    {
        if (!$answer = $mysqli->query("select unix_time, cast(active as unsigned integer) as activeI from uptimes where virus_id = \"" . $mysqli->escape_string($virus_id) . "\" order by unix_time")) return [];
        while ($row = $answer->fetch_assoc()) $uptimes[] = ["unix_time" => (int)$row["unix_time"], "active" => (int)$row["activeI"]];
    }

    function getHoursPerHourSlice(\Kelvinho\Virus\Singleton\Generator $uptimeGenerator, int $startTime, int $days) {
        $cycleDuration = 3600 / ($cyclesPerHour = 12); // in seconds
        $data = Torch::zeros(24); // string hour_interval => int hours
        // check over all 5 minute intervals. If analysis time is greater than next uptime in question, then update next uptime
        if (($uptime = $uptimeGenerator->next()) !== null) {
            $nextEntryUptime = $uptime["unix_time"];
            $active = 1 - @$uptime["active"];
            for ($cycle = 0; $cycle < $days * 24 * $cyclesPerHour + 2; $cycle++) {
                while ($startTime + $cycleDuration * $cycle > $nextEntryUptime) {
                    if (($uptime = $uptimeGenerator->next()) === null) break 2;
                    $nextEntryUptime = $uptime["unix_time"];
                    $active = 1 - ($uptime["active"] ?? $active);
                }
                $data[intdiv($cycle, $cyclesPerHour) % 24] += ($active) / $cyclesPerHour;
            }
        }
        return $data;
    }

    /**
     * Get a graphable 24-element array of {"hours" => ..., "label" => ...}
     *
     * @param array $uptimes
     * @param int $timezoneOffset
     * @param int $startTime unix time of start time to investigate
     * @return array the graphable array
     */
    function getDailyTimes(array $uptimes, int $timezoneOffset, int $startTime = -1) {
        $endTime = ($startTime = strtotime(date("Y-m-d H:00:00", ($startTime === -1) ? time() - 24 * 3600 : $startTime))) + 24 * 3600;
        // filtering out the uptimes that are not in the 1 day period
        $startState = 0; // the initial state just before the start time
        foreach ($uptimes as $uptime) if ($startTime < $uptime["unix_time"]) break; else $startState = $uptime["active"];
        $uptimes = filter($uptimes, fn($el) => $startTime < $el["unix_time"] && $endTime > $el["unix_time"]);
        array_unshift($uptimes, ["unix_time" => $startTime - 1, "active" => $startState]);
        $uptimes[] = ["unix_time" => $endTime];
        $data = getHoursPerHourSlice(new \Kelvinho\Virus\Singleton\Generator($uptimes), $startTime, 1);
        $labels = [];
        $hourSliceLabels = [0 => "00:00", 4 => "04:00", 8 => "08:00", 12 => "12:00", 16 => "16:00", 20 => "20:00"];
        for ($i = 0; $i < 24; $i++) {
            $data[$i] = min($data[$i], 1) * 100;
            $labels[$i] = $hourSliceLabels[fixHour($i + date("G", $startTime) + ((int)($timezoneOffset / 3600)))] ?? "";
        }
        return ["data" => $data, "labels" => $labels];
    }
    $timezoneOffset = $timezone->getOffset($user->getTimezone());

    function getMonthlyTimes(array $uptimes, int $timezoneOffset) {
        $startTime = strtotime(date("Y-m-d H:00:00", ($endTime = time()) - 30 * 24 * 3600));
        // filtering out the uptimes that are more than 1 month away
        $uptimes = filter($uptimes, fn($el) => $startTime < $el["unix_time"]);
        $uptimes[] = ["unix_time" => $endTime, "active" => 1];
        $data = getHoursPerHourSlice(new \Kelvinho\Virus\Singleton\Generator($uptimes), $startTime, 30);
        $graphable = [];
        for ($i = 0; $i < 24; $i++) $graphable[$i] = $data[fixHour($i - date("G", $startTime) - ((int)($timezoneOffset / 3600)))];
        return $graphable;
    }

    ?>
    const days = <?php echo "[" . join(", ", map(Torch::reverse(Torch::range(14, 1)), function ($daysBack) use ($uptimes, $timezoneOffset) {
            $graphableDaily = getDailyTimes(Torch::clone($uptimes), $timezoneOffset, $startTime = time() - $daysBack * 24 * 3600);
            return "{labels: " . json_encode($graphableDaily["labels"]) . ", data: " . json_encode($graphableDaily["data"]) . ", " .
                "title: \"" . date("M j", $startTime) . " - " . date("M j", $startTime + 24 * 3600) . "\"}";
        })) . "];"; ?>

        chartColors = {
            red: 'rgb(255, 99, 132)', orange: 'rgb(255, 159, 64)', yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)', blue: 'rgb(54, 162, 235)', purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };

    Chart.defaults.global.elements.point.radius = 0;

    let dayIndex = days.length - 1;

    const gridColor = "<?php echo $user->isDarkMode() ? "#ccc3" : "#ccca"; ?>";

    let dailyChart = new Chart(document.getElementById('dailyChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: days[dayIndex].labels,
            datasets: [{
                label: 'First dataset', backgroundColor: chartColors.purple, borderColor: chartColors.purple,
                borderWidth: 0, fill: true, data: days[dayIndex].data
            }]
        },
        options: {
            responsive: true, aspectRatio: 1.8, legend: {display: false},
            title: {display: true, text: "Daily active view (" + days[dayIndex].title + ")"},
            scales: {
                xAxes: [{ticks: {autoSkip: false}, gridLines: {color: gridColor}}],
                yAxes: [{
                    scaleLabel: {display: true, labelString: '% Hour'},
                    ticks: {beginAtZero: true, max: 100}, gridLines: {color: gridColor}
                }],
            }, animation: {duration: 500}
        }
    });

    function changeDayGraph(next) { // next: whether go to next day or not
        dayIndex += (dayIndex < days.length - 1) * next - (dayIndex > 0) * (1 - next);
        dailyChart.data.labels = days[dayIndex].labels;
        dailyChart.data.datasets[0].data = days[dayIndex].data;
        dailyChart.options.title.text = "Daily active view (" + days[dayIndex].title + ")";
        dailyChart.update();
    }

    new Chart(document.getElementById('monthlyChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ["00:00", "", "", "", "04:00", "", "", "", "08:00", "", "", "", "12:00", "", "", "", "16:00", "", "", "", "20:00", "", "", ""],
            datasets: [{
                label: 'Second dataset', backgroundColor: chartColors.green, borderColor: chartColors.green,
                borderWidth: 0, cubicInterpolationMode: 'monotone',
                data: <?php echo json_encode(getMonthlyTimes(Torch::clone($uptimes), $timezoneOffset)); ?>
            }]
        },
        options: {
            responsive: true, animation: {duration: 500},
            title: {display: true, text: "Monthly frequency view"}, aspectRatio: 1.8, legend: {display: false},
            scales: {
                xAxes: [{ticks: {autoSkip: false}, gridLines: {color: gridColor}}],
                yAxes: [{
                    scaleLabel: {display: true, labelString: 'Hours'},
                    gridLines: {color: gridColor}, ticks: {beginAtZero: true}
                }]
            }
        }
    });

    autoAdjustHeight($('#profile'));
</script>
</html>
