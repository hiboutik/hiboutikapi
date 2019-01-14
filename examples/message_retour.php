<?php
/**
 * Message retour : action_links silent & webhooks
 * -----------------------------------------------------------------------------
 */
 
 
header('Content-type: application/json; charset=utf-8');
$message_retour["alerte"] = "
<div class=\"alert alert-info alert-dismissable\">
<button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
<strong>Message retour test</strong>
</div>
";
echo json_encode($message_retour);
