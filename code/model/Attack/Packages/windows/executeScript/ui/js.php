<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExecuteScript;
use function Kelvinho\Virus\map;

/** @var ExecuteScript $attack */
?>
<!--suppress JSUnusedGlobalSymbols, Annotator -->
<script>
    const gui = {"extraWrapper": $("#extras-wrapper")};

    // extending jquery to focus cursor to the end of a text field
    (function($){
        $.fn.focusTextToEnd = function(){
            this.focus();
            const $thisVal = this.val();
            this.val('').val($thisVal);
            return this;
        }
    }(jQuery));

    // make the extras-url textarea auto adjust the height
    $('#extras-url').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;resize:none;');
    }).on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    const Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){let t="";let n,r,i,s,o,u,a;let f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){let t="";let n,r,i;let s,o,u,a;let f=0;e = e.replace(/\\+\\+[++^A-Za-z0-9+/=]/g, "");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!==64){t=t+String.fromCharCode(r)}if(a!==64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"n");let t="";for(let n=0;n<e.length;n++){let r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){let t="";let n=0;let r=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){let c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{let c2=e.charCodeAt(n+1);let c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}};

    function htmlspecialchars(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;")
            .replace(/\\/g, "&#92;");
    }

    function htmlspecialchars_decode(text) {
        return text
            .replace(/&amp;/g, `&`)
            .replace(/&lt;/g, `<`)
            .replace(/&gt;/g, `>`)
            .replace(/&quot;/g, `"`)
            .replace(/&#039;/g, `'`)
            .replace(/&#92;/g, `\\`);
    }

    class Extra {
        constructor(identifier = "", content = "") {
            this.identifier = identifier;
            this.content = Base64.decode(content);
        }

        setIdentifier(identifier) {
            this.identifier = identifier;
        }

        setContent(content) {
            this.content = content;
        }

        render(count) {
            return `
                <h5># ` + count + `</h5>
                <div class="w3-row">
                    <div class="l11 m10 s9 w3-col">
                        <input class="w3-input"
                            type="text"
                            id="identifier-` + count + `"
                            placeholder="Resource identifier #` + count + `, short, sweet, simple, alphanumeric (eg. entry, act1, license)"
                            value="` + this.identifier + `">
                    </div>
                    <div class="l1 m2 s3 w3-col">
                        <button class="w3-button w3-khaki"
                                id="delete-btn-` + count + `"
                                style="width: 100%">Delete</button>
                    </div>
                </div>
                <textarea class="w3-input contents"
                    id="content-` + count + `"
                    cols="80"
                    style="resize: vertical;"
                    placeholder="Content #` + count + `, literally anything you want">` + htmlspecialchars_decode(this.content) + `</textarea><br>`;
        }

        /**
         * Exports to JSON form so it can be processed by the backend.
         */
        export() {
            return {
                "identifier": this.identifier,
                "content": htmlspecialchars_decode(this.content)
            }
        }
    }

    class Extras {
        constructor(extras = []) {
            this.extras = extras;
        }

        addExtra(extra) {
            this.extras.push(extra);
        }

        delete(index) {
            if (this.extras.length !== 1) this.extras.splice(index, 1);
        }

        newLast() {
            this.extras.push(new Extra());
        }

        render() {
            gui.extraWrapper.html("");
            for (let i = 0; i < this.extras.length; i++) gui.extraWrapper.append(this.extras[i].render(i));
        }

        unbind() {
            for (let i = 0; i < this.extras.length; i++) {
                $("#delete-btn-" + i).off();
                $("#identifier-" + i).off();
                $("#content-" + i).off();
            }
        }

        /**
         * This is to save the state of every extras before deleting everything and recreate them
         */
        saveState() {
            for (let i = 0; i < this.extras.length; i++) {
                this.extras[i].setIdentifier($("#identifier-" + i).val());
                this.extras[i].setContent(htmlspecialchars($("#content-" + i).val()));
            }
        }

        /**
         * This should be called after a new render
         */
        rebind() {
            // bind all delete buttons except last one
            for (let i = 0; i < this.extras.length - 1; i++) {
                const finalI = i;
                $("#delete-btn-" + finalI).on("click", function () {
                    extras.saveState();
                    extras.unbind();
                    extras.delete(finalI);
                    extras.render();
                    extras.rebind();
                });
            }
            // bind last identifier text box
            const finalLast = this.extras.length - 1;
            $("#identifier-" + finalLast).on("input", function () {
                extras.saveState();
                extras.unbind();
                extras.newLast();
                extras.render();
                extras.rebind();
                $("#identifier-" + finalLast).focusTextToEnd();
            });
            // make the content sections lengthen automatically
            $('.contents').each(function () {
                this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;resize:none;');
            }).on('input', function () {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

        }

        /**
         * Exports to JSON form so it can be processed by the backend.
         */
        export() {
            extras.saveState();
            const answer = [];
            for (let i = 0; i < this.extras.length - 1; i++) {
                answer.push(this.extras[i].export());
            }
            return answer;
        }
    }

    let extras = new Extras([<?php echo implode(", ", map($attack->getExtras(), function ($extra) use ($attack) {
        return "new Extra(\"" . $extra["identifier"] . "\", `" . base64_encode($extra["content"]) . "`)";
    })); ?>]);
    extras.addExtra(new Extra());
    extras.render();
    extras.rebind();

</script>