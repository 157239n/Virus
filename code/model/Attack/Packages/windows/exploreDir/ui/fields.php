<?php

use Kelvinho\Virus\Attack\AttackInterface;

?>
<label for="depth">Max depth</label>
<input class="w3-input" type="text" id="depth"
       value="<?php echo $attack->getMaxDepth(); ?>" <?php echo($attack->isStatus(AttackInterface::STATUS_DORMANT) ? "" : "disabled"); ?>>
<br>
<label for="dir">Directory</label>
<input class="w3-input" type="text" id="dir"
       value="<?php echo $attack->getRootDir(); ?>" <?php echo($attack->isStatus(AttackInterface::STATUS_DORMANT) ? "" : "disabled"); ?>>
<br>