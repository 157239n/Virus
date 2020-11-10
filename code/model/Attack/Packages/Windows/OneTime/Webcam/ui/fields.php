<?php

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\Packages\Windows\OneTime\Webcam\Webcam;
use function Kelvinho\Virus\map;

/** @var Webcam $attack */

?>
<label for="duration">Duration</label>
<select id="duration" class="w3-select" name="option"
        style="padding: 10px;" <?php echo $attack->getStatus() === AttackBase::STATUS_EXECUTED ? "disabled" : ""; ?>>
    <?php echo implode(map(range(Webcam::MIN_DURATION, Webcam::MAX_DURATION, 10), fn($duration) => "<option value='$duration'>$duration seconds</option>")); ?>
</select>
<br><br>
