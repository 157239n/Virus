<?php

use Kelvinho\Virus\Singleton\Header;

$virus_id = $requestData->getCheck("vrs");
$attack_id = $requestData->getCheck("aks");

if (!$virusFactory->exists($virus_id)) Header::badRequest();
if (!$attackFactory->exists($attack_id)) Header::badRequest();
if (!$authenticator->authorized($virus_id, $attack_id)) Header::forbidden();

$session->set("virus_id", $virus_id);
$session->set("attack_id", $attack_id); ?>
<script>window.location = "<?php echo DOMAIN . "/attack"; ?>"</script>
