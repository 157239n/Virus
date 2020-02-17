<script>
    function openFile(fileName, desiredName) {
        window.location = "<?php echo DOMAIN . "/vrs/" . $attack->getVirusId() . "/aks/" . $attack->getAttackId() . "/ctrls/getFile"; ?>?file=" + fileName + "&desiredName=" + desiredName;
    }
</script>