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
 *   API Key & Email : Cliquez sur le A en haut à droite de votre compte Hiboutik
 *
 */



require __DIR__.'/../../HiboutikAPI/src/Hiboutik/HiboutikAPI/autoloader.php';

//API REST Hiboutik
$hiboutik_account = "";
$user = "";
$pass = "";


$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

if (!isset($_POST['sale_id'])) {print("Please provide a valid sale_id");exit;} else {$sale_id = $_POST['sale_id'];}

$result = $hiboutik->get("/sales/$sale_id");
if ($hiboutik->request_ok) {

//on récupère tous les produits de la vente
$line_items = $result[0]['line_items'];

//pour chaque ligne de la vente
foreach ($line_items as $valeur)
{
$line_item_id = $valeur['line_item_id'];
$quantity = $valeur['quantity'];
$product_price = $valeur['product_price'];

//calcul des points | règle 1 euro = 1 point
$points = $quantity * $product_price;
//éventuellement gestion de l'arrondi car les points ne peuvent être que des entiers (integer)

//mise à jour des points des produits de la vente
$data = array(
  "line_item_attribute" => "points",
  "new_value" => $points,
);
$update_points = $hiboutik->put("/sale_line_item/$line_item_id", $data);
}

header('Content-type: application/json; charset=utf-8');
$message_retour["alerte"] = "
<div class=\"alert alert-info alert-dismissable\">
<button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
<strong>Fidélité appliquée avec succès</strong>
</div>
";
echo json_encode($message_retour);
exit();


}
else
{
print 'An error has occured';
if (isset($result['details']['error_description'])) {
print ': '.$result['details']['error_description'];
} else {
print ': '.$result['error_description'];
}
}
