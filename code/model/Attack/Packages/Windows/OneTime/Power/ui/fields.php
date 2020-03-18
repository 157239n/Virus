<label for="type">Current style</label>
<input id="type" class="w3-input" type="text" value="<?php echo($attack->isShutdown() ? "Shutdown" : "Restart"); ?>"
       disabled>
<br>
<button class="w3-btn w3-red" onclick="toggle()">Toggle</button>
