<?php

/**
 * Responds to being disabled and turned off
 */

/** @var \Kelvinho\Virus\Attack\AttackBase $this */

$this->cancel();
$this->saveState();
