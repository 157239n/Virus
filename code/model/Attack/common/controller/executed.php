<?php

/**
 * Queries whether this attack is executed. Normally in order to refreshes the page automatically
 */

/** @var \Kelvinho\Virus\Attack\AttackBase $this */

echo $this->getStatus() == \Kelvinho\Virus\Attack\AttackBase::STATUS_EXECUTED ? "1" : "0";
