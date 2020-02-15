<?php

use Kelvinho\Virus\Attack\AttackBase;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\ActivateSwarm\ActivateSwarm $attack */

?>
<label for="baseLocation">Base location</label>
<input class="w3-input" type="text" id="baseLocation"
       value="<?php echo $attack->getBaseLocation(); ?>" <?php echo($attack->isStatus(AttackBase::STATUS_DORMANT) ? "" : "disabled") ?>>
<br>
<label for="libsLocation">Libs location</label>
<input class="w3-input" type="text" id="libsLocation"
       value="<?php echo $attack->getLibsLocation(); ?>" <?php echo($attack->isStatus(AttackBase::STATUS_DORMANT) ? "" : "disabled") ?>>
<br>
<label for="initialLocation">Initial location</label>
<input class="w3-input" type="text" id="initialLocation"
       value="<?php echo $attack->getInitialLocation(); ?>" <?php echo($attack->isStatus(AttackBase::STATUS_DORMANT) ? "" : "disabled") ?>>
<br>
<label for="swarmClockSpeed">Swarm clock speed</label>
<input class="w3-input" type="text" id="swarmClockSpeed"
       value="<?php echo $attack->getSwarmClockSpeed(); ?>" <?php echo($attack->isStatus(AttackBase::STATUS_DORMANT) ? "" : "disabled") ?>>
<br>
<label>
    Check file integrity
    <input class="w3-radio" type="radio" name="checkHash"
           value="1" <?php echo($attack->getCheckHash() ? "checked" : ""); ?>>
</label>
<label>
    Don't check file integrity
    <input class="w3-radio" type="radio" name="checkHash"
           value="0" <?php echo($attack->getCheckHash() ? "" : "checked"); ?>>
</label>
<br><br>