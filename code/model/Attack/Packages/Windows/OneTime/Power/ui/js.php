<script>
    function toggle() {
        const element = $("#type");
        switch (element.val()) {
            case "Shutdown":
                element.val("Restart");
                break;
            case "Restart":
                element.val("Shutdown");
                break;
            default:
                element.val("Restart");
                break;
        }
    }
</script>
