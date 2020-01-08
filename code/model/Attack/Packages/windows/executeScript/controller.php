<?php

$script = $this->requestData->postCheck("script");

$this->setScript($script);
$this->saveState();