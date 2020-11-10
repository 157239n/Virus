<?php

global $userFactory, $session;

$user = $userFactory->currentChecked();
if ($user->isHold()) { ?>
    <p>You are currently holding, which means you can't install a new virus until you remove the hold. Click <a
                href="<?php echo DOMAIN_CONTROLLER . "/removeHold/" . base64_encode(DOMAIN . "/attack"); ?>"
                class="link">here</a> to do so.</p>
<?php }
