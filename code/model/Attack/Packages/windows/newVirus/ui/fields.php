<?php

use Kelvinho\Virus\Attack\AttackInterface;

?>
<label for="newVirusId">New virus id</label>
<input class="w3-input" type="text" id="newVirusId"
       value="<?php echo $attack->getNewVirusId(); ?>" disabled>
<br>
<label for="baseLocation">Base location</label>
<input class="w3-input" type="text" id="baseLocation"
       value="<?php echo $attack->getBaseLocation(); ?>" <?php echo($attack->isStatus(AttackInterface::STATUS_DORMANT) ? "" : "disabled"); ?>>
<br>