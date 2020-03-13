<?php

/**
 * This is to scan for all attacks, all viruses and all users and coalesce every resource usage in the last day (optional), to then be billed
 */

use Kelvinho\Virus\Usage\Usage;

$now = time();
foreach ($userFactory->getAll() as $user_handle) {
    $user = $userFactory->get($user_handle);
    $usage = $user->usage();
    $user->setUnpaidAmount($user->getUnpaidAmount() + max($usage->getMoney() - Usage::MONTHLY_QUOTA, 0));
    $usage->resetDynamic($now);
    $usage->saveState();
    $user->saveState();
}
