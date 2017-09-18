<?php
/**
 *
 *  Reminders anniversaires
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet d'envoyer un mail (reminder) contenant la liste des clients dont c'est l'anniversaire le jour d'exécution du script
 *   API Key & Email : SETTINGS -> USERS -> Click on the wrench symbol.
 *
 */

//error_reporting(E_ALL);

$account = ""; //libellé de votre compte | http://www.logiciel-caisse-gratuit.com/ou-trouver-mon-numero-de-compte-ainsi-que-le-libelle-de-mon-compte/
$user = ""; //adresse email
$key = ""; //clé d'accès à l'API

$jour = date ("d");
$mois = date ("m");

require 'vendor/autoload.php';

try {

//est ce qu'on a tous les éléments permettant d'accéder à l'API ?
if (empty($account) OR empty($user) OR empty($key)) throw new Exception("Please provide a valid account, user & key", 75009);

//instanciation de l'API Hiboutik 
$hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $key);

//récupération des clients dont c'est l'anniversaire
$customers_birthday = $hiboutik->getHiboutik("customers_birthday/$mois/$jour");

//cas où il n'est pas possible d'accéder à l'API
if ($hiboutik->error)
{
$hiboutik_response = $hiboutik->response;
throw new Exception("$hiboutik_response", 75009);
}

//on vérifie qu'on a au moins un résultat
if (count($customers_birthday) > "0")
{
$contenu_du_mail = "";
foreach ($customers_birthday as $cle => $valeur)
{
$customers_id = $valeur -> customers_id;
$contenu_du_mail .= "\nanniversaire du client $customers_id";
}
mail("$user", "Anniversaires", "$contenu_du_mail", "From: contact@hiboutik.com");
}


} catch (Exception $e) {
    $message_retour = $e->getMessage();
  if ($e->getCode() === 75009) {
    header("HTTP/1.1 500 $message_retour");
    echo "Error : $message_retour";
  } else {
    error_log($message_retour, 0);
  }
}
