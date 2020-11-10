<?php

namespace Kelvinho\Virus\Singleton;

use Kelvinho\Virus\Attack\AttackBase;

class BackgroundAttacks {
    /**
     * Complex common styles for background attacks
     */
    public static function style(): void { ?>
        <!--suppress CssUnusedSymbol -->
        <style>
            .sidenav {
                height: calc(100% - 38px);
                width: 30%;
                position: fixed;
                z-index: 1;
                top: 38px;
                left: 0;
                background-color: #111;
                overflow-x: hidden;
                transition: 0.5s;
                padding-top: 60px;
            }

            .sidenav a {
                padding: 8px 8px 8px 32px;
                text-decoration: none;
                font-size: 17px;
                color: #818181;
                display: block;
                transition: 0.3s;
            }

            .sidenav a:hover {
                color: #f1f1f1;
            }

            .sidenav .closeBtn {
                position: absolute;
                top: 0;
                right: 25px;
                font-size: 36px;
                margin-left: 50px;
            }

            .navActive {
                background-color: #333333;
            }

            @media screen and (max-height: 450px) {
                .sidenav {
                    padding-top: 15px;
                }

                .sidenav a {
                    font-size: 18px;
                }
            }

            .sidenavClosed {
                left: -30%;
            }

            @media screen and (max-width: 1200px) {
                .sidenav {
                    width: 35%;
                }

                .sidenavClosed {
                    left: -35%;
                }
            }

            @media screen and (max-width: 950px) {
                .sidenav {
                    width: 50%;
                }

                .sidenavClosed {
                    left: -50%;
                }
            }

            @media screen and (max-width: 501px) {
                .sidenav {
                    width: 100%;
                }

                .sidenavClosed {
                    left: -100%;
                }
            }
        </style>
    <?php }

    /**
     * Complex common html for background attacks
     */
    public static function html(): void { ?>
        <div id="streamNav" class="sidenav sidenavClosed">
            <a href="javascript:void(0)" class="closeBtn" onclick="closeStreamNav()">&times;</a>
            <a id="streamNav-btnSave" style="cursor: pointer">Save</a>
            <div id="streamNav-list"></div>
        </div>
        <div id="savedNav" class="sidenav sidenavClosed">
            <a href="javascript:void(0)" class="closeBtn" onclick="closeSavedNav()">&times;</a>
            <a id="savedNav-btnForget" style="cursor: pointer">Forget</a>
            <div id="savedNav-list"></div>
        </div>
        <br>
        <div class="w3-row">
            <div class="w3-col l1 m2 s3" style="margin-top: 6px;">
                <span style="font-size:18px;cursor:pointer;" onclick="openStreamNav()" class="menuStreamSaved">&#9776; Daily</span>
            </div>
            <div class="w3-col l10 m8 s9" style="padding-right: 10px"><!--suppress HtmlFormInputWithoutLabel -->
                <input class="w3-input" id="streamMap-name" type="text"
                       placeholder="A name meaningful to you">
            </div>
            <div class="w3-col l1 m2 w3-hide-small">
                <button class="w3-btn w3-indigo" style="width: 100%"
                        onclick="context.streamEvents.updateContentName()">Update
                </button>
            </div>
        </div>
        <br>
        <div id="streamMap" class="w3-card-4"></div>

        <br>
        <div class="w3-row">
            <div class="w3-col l1 m2 s3" style="margin-top: 6px;">
                <span style="font-size:18px;cursor:pointer;" onclick="openSavedNav()" class="menuStreamSaved">&#9776; Saved</span>
            </div>
            <div class="w3-col l10 m8 s9" style="padding-right: 10px"><!--suppress HtmlFormInputWithoutLabel -->
                <input class="w3-input" id="savedMap-name" type="text"
                       placeholder="A name meaningful to you">
            </div>
            <div class="w3-col l1 m2 w3-hide-small">
                <button class="w3-btn w3-indigo" style="width: 100%"
                        onclick="context.savedEvents.updateContentName()">Update
                </button>
            </div>
        </div>
        <br>
        <div id="savedMap" class="w3-card-4"></div>
        <?php
    }

    /**
     * Complex common javascript for background attacks
     *
     * @param AttackBase $attack
     */
    public static function js(AttackBase $attack): void { ?>
        <!--suppress JSUnusedLocalSymbols -->
        <script>
            const gui = {
                "streamNav-list": $("#streamNav-list"),
                "savedNav-list": $("#savedNav-list"),
                "streamMap": $("#streamMap"),
                "savedMap": $("#savedMap"),
                "streamNav": $("#streamNav"),
                "savedNav": $("#savedNav"),
                "streamNav-btnSave": $("#streamNav-btnSave"),
                "savedNav-btnForget": $("#savedNav-btnForget"),
                "streamMap-name": $("#streamMap-name"),
                "savedMap-name": $("#savedMap-name")
            };

            const openStreamNav = () => gui.streamNav.removeClass("sidenavClosed");
            const closeStreamNav = () => gui.streamNav.addClass("sidenavClosed");
            const openSavedNav = () => gui.savedNav.removeClass("sidenavClosed");
            const closeSavedNav = () => gui.savedNav.addClass("sidenavClosed");

            class BaseEvent {
                /** Dummy constructor */
                constructor() {
                }

                /**
                 * Renders the HTML of the event
                 *
                 * @param {boolean} forStream Whether this is rendering for stream events.
                 * @return {string} HTML content
                 */
                renderContent(forStream) {
                }

                /** @return {string} Human friendly name, to be displayed in the navigation bar */
                getName() {
                }

                /** @param {string} name */
                setName(name) {
                }

                /** @return {string} Human friendly time, to be displayed in the navigation bar */
                getDisplayTime() {
                }

                /** @return {string} JSON string */
                //export = () => "";
                export() {
                }

                /** @param {Object} context Context to add. Optional, can override or not */
                addContext(context) {
                }
            }

            class StreamEvents {
                /** @param {Object} context */
                addContext(context) {
                    this.context = context;
                    /** @type {number} */ this.activeEventTime = context.allEvents.length < 1 ? 0 : context.allEventTimes[0];
                }

                unbindListeners() {
                    this.context.allEventTimes.forEach(unixTime => $("#streamNav-" + unixTime).off());
                    gui["streamNav-btnSave"].off();
                }

                rebindListeners() {
                    const self = this;
                    this.context.allEventTimes.forEach(unixTime => {
                        const finalUnixTime = unixTime;
                        $("#streamNav-" + finalUnixTime).on("click", () => (self.unbindListeners(), self.activeEventTime = finalUnixTime, self.render(), self.rebindListeners(), closeStreamNav()));
                    });
                    gui["streamNav-btnSave"].on("click", () => {
                        const self = this;
                        if (!this.context.allEventTimes.includes(self.activeEventTime)) return;
                        self.context.savedEvents.addEvent(self.activeEventTime);
                        this.context.savedEvents.updateServer(() => {
                            self.context.savedEvents.unbindListeners(), self.context.savedEvents.render(), self.context.savedEvents.rebindListeners(), closeStreamNav();
                            toast.display("Event saved successfully.");
                        }, () => toast.displayOfflineMessage("Can't save event."));
                    });
                }

                renderNav() {
                    const self = this;
                    gui["streamNav-list"].html("");
                    if (this.context.allEventTimes.length > 0)
                        this.context.allEventTimes.forEach(unixTime =>
                            gui["streamNav-list"].append("<a id='streamNav-" + unixTime + "' " +
                                "style='cursor:pointer' " +
                                "class='" + (unixTime === self.activeEventTime ? "navActive" : "") + "' >" + self.context.allEvents[unixTime].getDisplayTime() + (self.context.allEvents[unixTime].getName() ? ", " + self.context.allEvents[unixTime].getName() : "") + "</a>"));
                    else gui["streamNav-list"].append("<a>(No events available)</a>");
                    if (this.context.allEventTimes.includes(this.activeEventTime)) gui["streamMap-name"].val(this.context.allEvents[this.activeEventTime].getName());
                }

                renderContent() {
                    gui.streamMap.html("");
                    if (this.context.allEventTimes.includes(this.activeEventTime))
                        gui.streamMap.append(this.context.allEvents[this.activeEventTime].renderContent(true));
                }

                render() {
                    this.renderNav(), this.renderContent();
                }

                updateContentName() {
                    const self = this;
                    if (!this.context.allEventTimes.includes(this.activeEventTime)) return;
                    this.context.allEvents[this.activeEventTime].setName(gui["streamMap-name"].val());
                    this.context.uploadEvent(this.activeEventTime, () => {
                        self.context.streamEvents.unbindListeners(), self.context.savedEvents.unbindListeners();
                        self.context.streamEvents.renderNav(), self.context.savedEvents.renderNav();
                        self.context.streamEvents.rebindListeners(), self.context.savedEvents.rebindListeners();
                        toast.display("Updated!");
                    }, () => toast.displayOfflineMessage("Can't update!"));
                }
            }

            class SavedEvents {
                /**
                 * Constructs saved events with some initial events.
                 *
                 * @param {Array.<number>} eventTimes
                 */
                constructor(eventTimes) {
                    this.eventTimes = eventTimes;
                    this.activeEventTime = this.eventTimes.length < 1 ? 0 : this.eventTimes[0];
                }

                /** @param {Object} context */
                addContext(context) {
                    this.context = context
                }

                /**
                 * Removes event from the list of saved events
                 *
                 * @param {number} eventTime
                 */
                removeEvent(eventTime) {
                    for (let i = 0; i < this.eventTimes.length; i++) if (this.eventTimes[i] === eventTime) return this.eventTimes.splice(i, 1);
                    this.eventTimes.sort().reverse();
                }

                /**
                 * Adds event to the list of saved events.
                 *
                 * @param {number} eventTime
                 */
                addEvent(eventTime) {
                    if (!this.eventTimes.includes(eventTime)) this.eventTimes.push(eventTime);
                    this.eventTimes.sort().reverse();
                }

                unbindListeners() {
                    this.context.allEventTimes.forEach(unixTime => $("#savedNav-" + unixTime).off());
                    gui["savedNav-btnForget"].off();
                }

                rebindListeners() {
                    const self = this;
                    this.context.allEventTimes.forEach(unixTime => {
                        const finalUnixTime = unixTime;
                        $("#savedNav-" + finalUnixTime).on("click", () => {
                            self.unbindListeners(), self.activeEventTime = finalUnixTime;
                            self.render(), self.rebindListeners(), closeSavedNav();
                        });
                    });
                    gui["savedNav-btnForget"].on("click", () => {
                        if (!this.context.allEventTimes.includes(self.activeEventTime)) return;
                        self.removeEvent(self.activeEventTime);
                        self.updateServer(() => {
                            closeSavedNav(), self.unbindListeners(), self.render(), self.rebindListeners();
                            toast.display("Forgotten successful!");
                        }, () => toast.displayOfflineMessage("Can't forget event."));
                    });
                }

                renderNav() {
                    const self = this;
                    gui["savedNav-list"].html("");
                    if (this.eventTimes.length > 0)
                        this.eventTimes.forEach(unixTime =>
                            gui["savedNav-list"].append("<a id='savedNav-" + unixTime + "' " +
                                "style='cursor:pointer' " +
                                "class='" + (unixTime === self.activeEventTime ? "navActive" : "") + "' >" + this.context.allEvents[unixTime].getDisplayTime() + (this.context.allEvents[unixTime].getName() ? ", " + this.context.allEvents[unixTime].getName() : "") + "</a>"));
                    else gui["savedNav-list"].append("<a>(No events available)</a>");
                    if (this.context.allEventTimes.includes(this.activeEventTime)) gui["savedMap-name"].val(this.context.allEvents[this.activeEventTime].getName());
                }

                renderContent() {
                    gui.savedMap.html("");
                    if (this.context.allEventTimes.includes(this.activeEventTime))
                        new Promise((resolve, reject) => (gui.savedMap.append(this.context.allEvents[this.activeEventTime].renderContent(false)), resolve(0)));
                }

                render() {
                    this.renderNav(), this.renderContent();
                }

                export() {
                    return JSON.stringify(this.eventTimes);
                }

                updateServer(successCb, errorCb) {
                    $.ajax({
                        url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/updateSaved"; ?>",
                        type: "POST", data: {savedEvents: this.export()},
                        success: () => successCb(), error: () => errorCb()
                    });
                }

                updateContentName() {
                    const self = this;
                    if (!this.context.allEventTimes.includes(this.activeEventTime)) return;
                    this.context.allEvents[this.activeEventTime].setName(gui["savedMap-name"].val());
                    this.context.uploadEvent(this.activeEventTime, () => {
                        self.context.streamEvents.unbindListeners(), self.context.savedEvents.unbindListeners();
                        self.context.streamEvents.renderNav(), self.context.savedEvents.renderNav();
                        self.context.streamEvents.rebindListeners(), self.context.savedEvents.rebindListeners();
                        toast.display("Updated!");
                    }, () => toast.displayOfflineMessage("Can't update!"));
                }
            }

            /**
             * Setup everything. This is tucked away in a function because this can only be loaded after streamEvents and
             * savedEvents are defined, meaning allEvents and savedEventTimes must be defined.
             *
             * @param {Object.<number, BaseEvent>} allEvents
             * @param {Array.<number>} savedEventTimes
             */
            function setupBackgroundAttacksJs(allEvents, savedEventTimes) {
                /** @type Object */ const context = {
                    /**
                     * Uploads a modified event to the server. Used for changing the name.
                     *
                     * @param {number} unixTime
                     * @param successCb
                     * @param errorCb
                     */
                    "uploadEvent": (unixTime, successCb, errorCb) => {
                        $.ajax({
                            url: "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/updateEvent"; ?>",
                            type: "POST", data: {event: allEvents[unixTime].export(), unixTime: unixTime},
                            success: () => successCb(), error: () => errorCb()
                        });
                    },
                    /** @type Object.<number, BaseEvent> */
                    "allEvents": allEvents,
                    /** @type Array.<number> */
                    "allEventTimes": Object.keys(allEvents).map(x => +x).reverse()
                }
                /** @type {StreamEvents} */ const streamEvents = new StreamEvents();
                /** @type {SavedEvents} */ const savedEvents = new SavedEvents(savedEventTimes);
                context["streamEvents"] = streamEvents;
                context["savedEvents"] = savedEvents;
                Object.values(allEvents).forEach(/** @type BaseEvent */singleEvent => singleEvent.addContext(context));
                streamEvents.addContext(context), savedEvents.addContext(context);
                streamEvents.render(), savedEvents.render();
                streamEvents.rebindListeners(), savedEvents.rebindListeners();

                $(document).on("keydown", event => (event.key === "Escape") ? (closeStreamNav(), closeSavedNav()) : 0);
                const enterKeyCode = 13;
                gui["streamMap-name"].on('keypress', event => event.which === enterKeyCode ? streamEvents.updateContentName() : 0);
                gui["savedMap-name"].on('keypress', event => event.which === enterKeyCode ? savedEvents.updateContentName() : 0);
                return context;
            }
        </script>
    <?php }
}
