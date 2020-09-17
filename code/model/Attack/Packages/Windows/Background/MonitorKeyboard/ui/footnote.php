<h2>Keys</h2>
<?php \Kelvinho\Virus\Singleton\BackgroundAttacks::html(); ?>
<h2>Explanation</h2>
<p>This attack is meant to run continuously in the background, and will get what the host is typing every hour. The data
    is then sent back continuously and is ready to be viewed. There are 2 views: "daily", which has all the key pages in
    the past 24 hours, and the other, "saved" has all the key pages that you have intentionally saved. Some keys are
    thus shared between 2 views. Because the key pages get automatically deleted after 24 hours, if you wish to take
    note of an interesting page, you must save it. If you no longer need a saved page, there is an option to forget
    about it. Also for each key page, you can assign it a short name so that you can remember it easier.</p>
<p>Please note that this attack involve compiling a C# script on their machine using the existing toolchain. This is to
    minimize being identified by the operating system as malicious code. Extensive tests have shown that antiviruses
    usually don't flag the compiled script. However, this is still risky, so continue if you really wish to.</p>
<p>An interesting phenomenon is that sometimes the virus won't report every hour. When the host computer first started
    up, it will report right away and wait for the next hour to report. Thus, if the host computer restarts multiple
    times then there can be multiple reports in an hour.</p>