<?php
/**
 *
 *  Modification du vendeur de la vente en fonction de l'utilisateur a l'origine de la cloture
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet de modifier le vendeur d'une vente a la cloture de cette dernière. A confugurer dans Paramètres / Call back ventes
 *   API Key & Email : SETTINGS -> USERS -> Click on the wrench symbol.
 *
 */

$account = ""; //libellé de votre compte | http://www.logiciel-caisse-gratuit.com/ou-trouver-mon-numero-de-compte-ainsi-que-le-libelle-de-mon-compte/
$user = ""; //adresse email
$key = ""; //clé d'accès à l'API

require 'vendor/autoload.php';

try {

//on vérifie qu'on récupère bien la variable order_id par POST (URL de callback sur ventes)
if (!isset($_POST['sale_id'])) {throw new Exception("Please provide a valid sale_id");} else {$sale_id = $_POST['sale_id'];}
if (!isset($_POST['user_id'])) {throw new Exception("Please provide a valid user_id");} else {$user_id = $_POST['user_id'];}
if (!isset($_POST['vendor_id'])) {throw new Exception("Please provide a valid vendor_id");} else {$vendor_id = $_POST['vendor_id'];}

//est ce qu'on a tous les éléments permettant d'accéder à l'API ?
if (empty($account) OR empty($user) OR empty($key)) throw new Exception("Please provide a valid account, user & key");

//instanciation de l'API Hiboutik 
$hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $key);

if ($user_id <> $vendor_id)
{
//mise à jour du vendeur si nécessaire
$data = array(
  "sale_attribute" => "vendor_id",
  "new_value" => $user_id,
);
$update_vendor = $hiboutik->putHiboutik("sale/$sale_id", $data);
}

} catch (Exception $e) {
    $message_retour = $e->getMessage();
    error_log($message_retour, 0);
}

