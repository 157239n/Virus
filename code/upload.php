<?php
if (isset($_FILES["file"])) {
    exec("mv " . $_FILES["file"]["tmp_name"] . " " . __DIR__ . "/file");
}