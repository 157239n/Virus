<?php

use Kelvinho\Virus\Attack\AttackInterface;

?>

<label for="script">Script</label>
<textarea id="script" rows="12" cols="80" class="w3-input"
          style="resize: vertical;" <?php echo($attack->getStatus() != AttackInterface::STATUS_DORMANT ? "disabled" : ""); ?>><?php echo $attack->getScript(); ?></textarea>
<br>