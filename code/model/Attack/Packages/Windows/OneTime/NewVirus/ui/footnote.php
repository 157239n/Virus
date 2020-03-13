<?php

$user = $userFactory->get($session->get("user_handle"));
if ($user->isHold()) { ?>
    <p>You are currently holding, which means you can't install a new virus until you remove the hold. Click <a
                href="<?php echo DOMAIN_CONTROLLER . "/removeHold/" . base64_encode(DOMAIN_ATTACK_INFO); ?>"
                class="link">here</a> to do so.</p>
<?php }
