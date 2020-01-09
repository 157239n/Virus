<?php

use Kelvinho\Virus\Attack\AttackBase;

?>
<label for="directories">Directories</label><textarea id="directories" rows="12" cols="80" class="w3-input"
                                                      style="resize: vertical;"<?php echo($attack->getStatus() != AttackBase::STATUS_DORMANT ? "disabled" : ""); ?>><?php echo $attack->getDirectoriesAsBlock(); ?></textarea>
<br>