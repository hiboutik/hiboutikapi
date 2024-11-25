<?php
/**
 *
 *  reminder_planning.php
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *
 * Le script va :
 * - Récupérer les évênements dans le planning pour le lendemain
 * - Envoyer un reminder au client pour son rdv
 *
 */



require __DIR__.'/../../HiboutikAPI/src/Hiboutik/HiboutikAPI/autoloader.php';

$hiboutik_account = "";
$user = "";
$pass = "";

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

$planning = $hiboutik->get("/calendar/events/1/".date("Y/m/d", strtotime('+1 day'))."");


foreach ($planning as $mon_rdv) //pour chaque entrée dans le planning
{
if ($mon_rdv['customer_id'] <> "0") //si un client est lié
{
$infos_client = $hiboutik->get("/customer/".$mon_rdv['customer_id'].""); //on récupère les infos relatives au client
if (filter_var($infos_client[0]['email'], FILTER_VALIDATE_EMAIL)) //si le mail est valable
{
mail($infos_client[0]['email'], "Sujet du mail", "Votre rendez vous demain", "From: expediteur@domain.com"); //on envoie un reminder par email
}
}
}
