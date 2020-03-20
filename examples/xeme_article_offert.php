<?php
/**
 *
 *  xeme_article_offert.php
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet de de faire une offre x articles achetés, le moins cher offert
 * 	 Développé pour l'association Gresy https://www.gresy.fr/
 *
 * Le script va :
 * - Récupérer les infos relatives a la vente
 * - Trier les produits par prix
 * - Mettre le prix a zéro sur un article sur x (le moins cher)
 *
 */

require 'vendor/autoload.php';

//API REST Hiboutik : https://www.logiciel-caisse-gratuit.com/api-rest-hiboutik-activation/
$hiboutik_account = "";
$user = "";
$pass = "";

$nieme_article_offert = 3;

$le_message_retour = "";

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);


if (isset($_POST['sale_id']))
{
$sale_id = $_POST['sale_id'];
$sale_details = $hiboutik->get("/sales/$sale_id");

if ($hiboutik->request_ok) {
//on récupère tous les produits de la vente
$line_items = $sale_details[0]['line_items'];

$nb_articles = count($line_items);
$nb_articles_offerts = intdiv($nb_articles, $nieme_article_offert);
$my_items = array();
$my_items_name = array();
foreach($line_items as $line_item)
{
$my_items[$line_item['line_item_id']] = $line_item['product_price'];
$my_items_name[$line_item['line_item_id']] = $line_item['product_model'];
}
asort($my_items);

$i = 1;
foreach($my_items as $key => $value)
{
if ($i <= $nb_articles_offerts)
{
$change_price_product = $hiboutik->put("/sale_line_item/$key/", [
'line_item_attribute' => 'product_price',
'new_value' => 0
]);
if ($le_message_retour <> "") $le_message_retour .= "<br />";
$le_message_retour .= "Remise appliquée sur le produit $my_items_name[$key]";
}
$i++;
}

}
else
{
$le_message_retour = "Connexion error";
}

}

if ($le_message_retour <> "")
{
header('Content-type: application/json; charset=utf-8');
$message_retour["alerte"] = "
<div class=\"alert alert-info alert-dismissable\">
<button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
<strong>$le_message_retour</strong>
</div>
";
echo json_encode($message_retour);
}

