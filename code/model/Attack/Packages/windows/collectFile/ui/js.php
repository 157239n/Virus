<script>
    function openFile(fileName, desiredName) {
        window.location = "<?php echo DOMAIN_CONTROLLER . "/getFile"; ?>?file=" + fileName + "&desiredName=" + desiredName;
    }
</script>