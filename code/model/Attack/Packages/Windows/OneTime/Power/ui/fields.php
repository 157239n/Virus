<label for="type">Current style</label>
<input id="type" class="w3-input" type="text" value="<?php echo($attack->isShutdown() ? "Shutdown" : "Restart"); ?>"
       disabled>
<br>
<div class="w3-button w3-red" onclick="toggle()">Toggle</div>