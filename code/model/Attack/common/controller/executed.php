<?php

/**
 * Queries whether this attack is executed. Normally in order to refreshes the page automatically
 */

/** @var AttackBase $this */

use Kelvinho\Virus\Attack\AttackBase;

echo $this->getStatus() == AttackBase::STATUS_EXECUTED ? "1" : "0";
