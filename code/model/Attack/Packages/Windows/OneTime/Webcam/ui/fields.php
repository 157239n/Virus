<?php

use Kelvinho\Virus\Attack\Packages\Windows\OneTime\Webcam\Webcam;
use function Kelvinho\Virus\map;

/** @var Webcam $attack */

?>
<label for="duration">Duration</label><select id="duration" class="w3-select" name="option" style="padding: 10px;">
    <?php map(range(Webcam::MIN_DURATION, Webcam::MAX_DURATION, 10), function ($duration) { ?>
        <option value="<?php echo $duration; ?>"><?php echo "$duration seconds"; ?></option>
    <?php }); ?>
</select>
<br><br>
