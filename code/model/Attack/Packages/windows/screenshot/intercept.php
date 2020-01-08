<?php
if (isset($_FILES["screenshot"])) {
    exec("mv \"" . $_FILES["screenshot"]["tmp_name"] . "\" " . DATA_FILE . "/attacks/" . $this->getAttackId() . "/screen.png");
    $this->setExecuted();
    $this->saveState();
}
