<?php

/**
 * This is to scan for all attacks, all viruses and all users and coalesce every resource usage in the last day (optional), to then be billed
 */

$now = time();
foreach ($userFactory->getAll() as $user_handle) {
    $user = $userFactory->get($user_handle);
    foreach ($user->getViruses() as $blob) {
        $virus = $virusFactory->get($blob["virus_id"]);
        // loop through all executed attacks with static metrics, dynamic values inside attack's usage will get set to 0 automatically
        foreach ($virus->getAttacksByTime($virus->usage()->getLastUpdated(), $now) as $attack_id) {
            $attack = $attackFactory->get($attack_id);
            //if ($attack->getVirusId() === $virus->getVirusId())
            $virus->usage()->add($attack->usage());
            $user->usage()->add($attack->usage());
            $attack->usage()->resetDynamic($now)->saveState();
        }
        // loop through all deployed attacks with dynamic metrics
        foreach ($virus->getAttacksByTime(0, 1) as $attack_id) {
            $attack = $attackFactory->get($attack_id);
            //if ($attack->getVirusId() === $virus->getVirusId())
            $virus->usage()->addDynamic($attack->usage());
            $attack->usage()->resetDynamic($now)->saveState();
        }
        $user->usage()->addDynamic($virus->usage());
        $virus->usage()->resetDynamic($now)->saveState();
    }
    $user->usage()->saveState();
}
