<h2>Maps</h2>
<div id="streamSidenav" class="sidenav sidenavClosed">
    <a href="javascript:void(0)" class="closeBtn" onclick="closeStreamNav()">&times;</a>
    <a id="streamNav-save" style="cursor: pointer">Save</a>
    <div id="streamSidenavList"></div>
</div>
<div id="savedSidenav" class="sidenav sidenavClosed">
    <a href="javascript:void(0)" class="closeBtn" onclick="closeSavedNav()">&times;</a>
    <a id="savedNav-forget" style="cursor: pointer">Forget</a>
    <div id="savedSidenavList"></div>
</div>

<br>
<div class="w3-row">
    <div class="w3-col l1 m2 s3"><span style="font-size:20px;cursor:pointer"
                                       onclick="openStreamNav()">&#9776; Daily</span>
    </div>
    <div class="w3-col l10 m8 s9"><input class="w3-input" id="streamNavName" type="text"
                                         placeholder="A name meaningful to you"></div>
    <div class="w3-col l1 m2 w3-hide-small">
        <div class="w3-button w3-indigo" style="width: 100%" onclick="streamEvents.updateMapName()">Update</div>
    </div>
</div>
<br>
<div id="streamMap"></div>

<br>
<div class="w3-row">
    <div class="w3-col l1 m2 s3"><span style="font-size:20px;cursor:pointer"
                                       onclick="openSavedNav()">&#9776; Saved</span>
    </div>
    <div class="w3-col l10 m8 s9"><input class="w3-input" id="savedNavName" type="text"
                                         placeholder="A name meaningful to you"></div>
    <div class="w3-col l1 m2 w3-hide-small">
        <div class="w3-button w3-indigo" style="width: 100%" onclick="savedEvents.updateMapName()">Update</div>
    </div>
</div>
<br>
<div id="savedMap"></div>

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