<?php

use Kelvinho\Virus\Singleton\Header;

$virus_id = $requestData->getCheck("vrs");

if (!$virusFactory->exists($virus_id)) Header::badRequest();
if (!$authenticator->authorized($virus_id)) Header::forbidden();

$session->set("virus_id", $virus_id); ?>
<script>window.location = "<?php echo DOMAIN . "/virus"; ?>"</script>
