<?php
/**
 * Responds to the big update button
 */

/** @var \Kelvinho\Virus\Attack\AttackBase $this */

$this->setName($this->requestData->postCheck("name"))->setProfile($this->requestData->postCheck("profile"))->saveState();
