<?php
/**
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet d'envoyer une alerte si le nombre de ventes en attente est trop élevé
 *
 */

require __DIR__.'/../../HiboutikAPI/src/Hiboutik/HiboutikAPI/autoloader.php';

//API REST Hiboutik
$hiboutik_account = "vergerspetit";
$user = "";
$pass = "";

$nb_max_ventes = 5;

try {
//ouverture de la connexion à l'API
$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

//récupération des ventes en attente
$nb_open_sales = $hiboutik->get("/open_sales/".$_POST['shop_id']."");

if (count($nb_open_sales) > $nb_max_ventes)
{
header('Content-type: application/json; charset=utf-8');
$message_retour["alerte"] = "
<div class=\"alert alert-danger alert-dismissable\">
<button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
<strong>Attention vous avez trop de ventes en attente (".count($nb_open_sales).")</strong>
</div>
<audio src=\"https://static.hiboutik.com/sounds/0758.ogg\" autoplay>
  Votre navigateur ne supporte pas l'élément <code>audio</code>.
</audio>
";
echo json_encode($message_retour);
}

}
catch (\Exception $e)
{
$error = $e->getMessage();
$code_error = $e->getCode();
}

