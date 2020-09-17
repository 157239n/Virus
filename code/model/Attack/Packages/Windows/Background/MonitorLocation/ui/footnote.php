<h2>Maps</h2>
<?php \Kelvinho\Virus\Singleton\BackgroundAttacks::html(); ?>
<h2>Explanation</h2>
<p>This attack is meant to run continuously in the background, and will get the host's physical location every
    hour. The data is then sent back continuously and is ready to be viewed in a map. There are 2 map views: "daily",
    which has all the locations in the past 24 hours, and the other, "saved" has all the locations that you have
    intentionally saved. Some maps are thus shared between 2 map views. Because the locations get automatically deleted
    after 24 hours, if you wish to take note of an interesting location, you must save it. If you no longer need a saved
    map view, there is an option to forget about it. Also for each map, you can assign it a short name so that you can
    remember it easier.</p>
<p>An interesting phenomenon is that sometimes the virus won't report every hour. When the host computer first started
    up, it will report right away and wait for the next hour to report. Thus, if the host computer restarts multiple
    times then there can be multiple reports in an hour.</p>
