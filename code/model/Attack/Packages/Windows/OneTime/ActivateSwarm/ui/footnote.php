<h1>Explanation</h1>
<p>This attack is complicated. And dangerous too. If you do not know anything about programming stuff or the target
    is someone who is not tech-savvy, I suggest you never touch this package, because you might make the system
    unstable, or you might make the virus easily detectable. The TL;DR version of this is that it installs another
    virus that has the capability of fighting back. If it gets deleted, it will pick another location and replicate
    itself over there. If it gets stopped from the task manager, it will automatically clean up the old copy, and
    will again replicate itself to another completely random location. In short, it cannot be deleted if you really
    intend it, and that also means that even you don't have any control over it.</p>
<p>Again, please <span style="color: red;">do not use this package if you don't know what you are doing</span>
    because you will not have control over it.</p>
<h2>The parameters</h2>
There are a few parameters you can tweak:
<ul>
    <li><b>Base location</b>: This determines what folder the virus will pick a random location in and replicate.
        You should aim this to be as critical (meaning the folder is valuable, and the "user" don't really want to
        delete it) as possible (like C:\temp, or %appData%). Because the virus will die if the base location is
        deleted in 1 go, you'd want the area of damage to be as great as possible before the virus gives in. Because
        there will be leftovers time over time, you probably don't want this to be a folder the target use often.
        Another option is to make this appear as a legit application. For example, if they don't have Minesweeper,
        you can create a dummy Minesweeper folder structure in %appData%, trying to make it look as legit as
        possible using adv.ExecuteScript, and put your base location there. This does not cover "critical" folders,
        meaning you can actually delete the entire thing, but it can hide well, which may be something that you
        value.
    </li>
    <li><b>Libs location</b>: This is the location where your payloads (aka attacks) and binaries will be at. You
        should put this in a really specific corner of the computer. If the target computer has Adobe Acrobat, or
        something like that at D:\Apps\Adobe\, you might want to hide it at
        D:\Apps\Adobe\src\Templates\Main\View\utils. The idea is to make things blend into the background as much as
        possible. Also make sure the location is clear of any existing files because if there are, the existing
        application you reside in might malfunction and you might get detected.
    </li>
    <li><b>Initial location</b>: This is where the first script is going to be installed at. Because this lies
        outside of
        the replication system, this does not have to be within base location. You can put this anywhere that the
        simple virus has access to. The best thing to do is the same as the libs location, meaning you should put it
        in a very obscure and particular place.
    </li>
    <li><b>Swarm clock delay</b>: This is how fast (in seconds) the entire swarm checks on each other. This can be
        any integer, but to serve its purpose, the values should be either 1, 2, or 3. The recommended value is <b>2
            seconds</b>. Values higher than 3 just doesn't make sense. If deleting the base location takes more than
        2 seconds, then a swarm with a clock delay of 1s will be able to recover. If deleting the base location
        takes more than 4 seconds, then a swarm with a clock delay of 3s will be able to recover. Likewise, if
        stopping all of the swarm through task manager takes more than 2 seconds, then a swarm with a clock delay of
        1s will be able to recover. The lower the number, the faster it can respond and auto-heal itself. However,
        the lower the number, the higher the CPU usage will be, and the more likely it gets detected. A delay of 1
        will consume <b>8-14%</b> (don't check file integrity) and <b>10-30%</b> (check file integrity). A delay of
        2 will consume <b>3-8%</b> (don't check file integrity) and <b>4-13%</b> (check file integrity).
    </li>
    <li><b>Check file integrity</b>: This determines whether the virus will check the script's contents. If this is
        disabled, only deleting and stopping will cause the system to auto-heal. If this is enabled, then the swarm
        will also check whether the scripts have been modified in any way. If it is modified then it will begin to
        auto-heal. The recommended option is to disable this, because realistically, no one will be bothered to go
        in and change the code around. They will normally just straight up delete it. Enabling this requires more
        CPU time, thus more battery usage, thus the swarm is easier to be detected.
    </li>
</ul>
<h2>Controlling it</h2>
<p>It's fairly easy to control the swarm. It acts as if it's a simple virus, and you can still deploy every attack
    packages like you used to. Everything is the same. The user interface is the same, the payloads sent to it are
    also the same. The only difference is that you cannot make it to self destruct or update the code itself.</p>
<h2>The problem</h2>
<p>The simple virus beforehand have 2 components: a main script, located at the virus's home, and a startup script,
    located at the startup folder. Windows will execute every script and open every file in the startup folder when
    turning on the computer. So, the startup script will execute our main script, which will repeatedly download new
    payloads, execute them, and sending the results back.</p>
<p>So now, what happens when someone kill the virus from the task manager? The process will stop, but the files
    remain there. If the user then restarts the computer, it will run again. But what if someone deletes the startup
    script? The next time they start up their computer, the main script will not be run, and our virus effectively
    dies. What happens when someone deletes the main script? Well it will be killed immediately, and on the next
    boot, the startup script can't activate it, and again, our virus is dead.</p>
<h2>The solution</h2>
<p>How can we mitigate this? There are many ways this can be mitigated, but here is our way of doing it. There will
    be 3 components to the swarm: the naked script, the hidden script, and the startup script. The naked script and
    the hidden script is exactly the same. When the simple virus installs the swarm, it first creates the naked
    script at the "initial location". The naked script then detects that there are no startup script, and will
    create that. It also detects that there are no hidden script either, so it choose a random location inside "base
    location", copies itself over. That new script is now the hidden script.</p>
<p>The naked script does not know where the hidden script is at. It just creates it at a random location then
    abandon it there. This means that if someone know where the naked script is, they cannot know where the hidden
    script is, because the naked one has no memory of it. Now, the hidden script will keep check on both the naked
    script and the startup script. If they are not there, or if their contents are modified, proving that someone is
    tampering the virus and trying to reverse engineer it, or if the naked script seems to be killed off by task
    manager, it will delete the naked and the startup script immediately.</p>
<p>Likewise, if the naked script detects that the hidden script is not around and running, it will proceeds to
    create a new one. This is quite remarkable, considering that the naked script doesn't even know where the hidden
    script is but still have a way of communicating with it.</p>
<h2>Caveats and things to look out for</h2>
<p>This architecture assumes that you cannot delete both the hidden script and the naked script at the same time. It
    also assumes that you cannot stop both viruses at the same time. If you can do either of them, the virus will
    not be able to fight back.</p>
<p>Choosing clock delay is hard. If it's too low (1), it will use CPU like crazy, and thus drain the battery pretty
    quickly, leaving a fairly noticeable effect. If it's too high (3), then it is vulnerable to being killed off
    from task manager because you can do that quite quickly.</p>
<p>The swarm intends to sort of not have any social engineering strategies. This is because the swarm will be
    launched using either a simple virus or another swarm, meaning at no point should the user ever encounters this,
    and if the user encounters it, because parts of it are separated everywhere, it's almost impossible to know
    those parts are all working together and if the user decides to delete it, no harm no foul. So, while the idea
    of the simple virus is to appear as if it's a legit application to hide itself, the idea of the swarm is to not
    appear as an application at all, and that if its files are found out and deleted, nothing happens. Note that
    because of all these caveats, if the target you are using this against have no idea how computers work at all,
    you should just go with the simple virus and it will be more stable and perform better than the swarm.</p>
<p>Because the naked script does not know where the hidden script is (and it's required not to know to not
    compromise the system), this also means that the naked script can't delete the hidden script. What this means is
    that each reboot will cause the hidden script to be left behind, with no way of cleaning it up and avoid
    detection. This is why you should choose the base location to be a place where the target never interacts with.
    If the user come across it, although they can't delete it because it will just auto-heal, this might make them
    suspicious of you of doing something to their computer, or they might try to take bigger precautions like
    installing multiple antiviruses, which might hinder the virus more.</p>
<p>There exists also another very subtle behavior of the swarm. If you repeatedly kill them one by one in like 5
    seconds, and they keep healing, they won't pick a random location to replicate into anymore. This is because
    picking a random location is a time consuming task which can take from 10 seconds to 4 minutes to a really wide
    base location, each time the swarm replicates, it sends the last place it has scouted to the newly replicated
    virus and that virus will start to scout out for another random location. However, if it is killed then it can't
    scout for a new location, and thus is stuck with the initial random location that it was given to. Very likely
    this won't affect you, but it's still a behavior which you might want to plan around.</p>