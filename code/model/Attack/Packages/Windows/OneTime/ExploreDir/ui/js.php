<script>
    function toggle(id) {
        const element = $("#" + id);
        if (element.css("display") === "block") element.css("display", "none");
        else element.css("display", "block");
    }

    function collapseAll() {
        $(".folding").css("display", "none");
    }

    function copyToClipboard(textToCopy) {
        console.log("recorded");
        const tmpCopyPlace = $("#copyPlace");
        tmpCopyPlace.val(textToCopy);
        tmpCopyPlace.css("display", "");
        let copyPlace = document.getElementById("copyPlace");
        copyPlace.select();
        document.execCommand("copy");
        tmpCopyPlace.css("display", "none");
        toast.display("Copied!");
    }
</script>
