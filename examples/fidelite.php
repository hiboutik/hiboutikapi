<?php
/**
 *
 *  Points fidélité
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet de définir les points fidélité a la clôture d'une vente en fonction du prix des produits vendus selon la règle 1 euro = 1 point
 *   API Key & Email : SETTINGS -> USERS -> Click on the wrench symbol.
 *
 */

//error_reporting(E_ALL);

$account = ""; //libellé de votre compte | http://www.logiciel-caisse-gratuit.com/ou-trouver-mon-numero-de-compte-ainsi-que-le-libelle-de-mon-compte/
$user = ""; //adresse email
$key = ""; //clé d'accès à l'API


require 'vendor/autoload.php';

try {

//on vérifie qu'on récupère bien la variable order_id par POST (URL de callback sur ventes)
if (!isset($_POST['order_id'])) {throw new Exception("Please provide a valid order_id", 75009);} else {$order_id = $_POST['order_id'];}

//est ce qu'on a tous les éléments permettant d'accéder à l'API ?
if (empty($account) OR empty($user) OR empty($key)) throw new Exception("Please provide a valid account, user & key", 75009);

//instanciation de l'API Hiboutik 
$hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $key);

//récupération des informations associées a la vente
$order_details = $hiboutik->getHiboutik("sales/$order_id");

//cas où il n'est pas possible d'accéder à l'API
if ($hiboutik->error)
{
$hiboutik_response = $hiboutik->response;
throw new Exception("$hiboutik_response", 75009);
}

//on vérifie qu'on a bien un résultat pour notre appel à l'API
if (count($order_details) <> "1")
{
$hiboutik_response = "Should obtain only one result but we fond : " . count($order_details);
throw new Exception("$hiboutik_response", 75009);
}

$line_items = $order_details[0]->line_items;

//récupération des lignes de la vente
foreach ($line_items as $cle => $valeur)
{
$detail_commande_id = $valeur -> detail_commande_id;
$quantity = $valeur -> quantity;
$product_price = $valeur -> product_price;

//calcul des points | règle 1 euro = 1 point
$points = $quantity * $product_price;
//éventuellement gestion de l'arrondi car les points ne peuvent être que des entiers (integer)

$hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $key);

//mise à jour des points des produits de la vente
$data = array(
  "line_item_attribute" => "points",
  "new_value" => $points,
);
$create_a_sale = $hiboutik->putHiboutik("sale_line_item/$detail_commande_id", $data);

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




?>
