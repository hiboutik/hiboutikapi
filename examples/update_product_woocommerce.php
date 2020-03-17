<?php
/**
 *
 *  Update produit Hiboutik --> Woocommerce
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet de mettre à jour les produits de votre Woocommerce suite aux modifications effectuées sur Hiboutik 
 *
 *   Sur votre serveur installez les librairies de l'API Hiboutik : https://github.com/hiboutik/hiboutikapi ainsi que celle permettant d'accéder à l'API Woocommerce : https://packagist.org/packages/automattic/woocommerce
 *   Mettez en place le script ci-dessous et indiquez son url comme webhook produit
 * 
 * Le script va :
 * - Récupérer l'identifiant produit envoyé par le webhook.
 * - Eécupérer les infos relatives au produit via API
 * - Faire les modifications sur le woocommerce (ici le nom du produit)
 *
 */



error_reporting(E_ALL);

require 'vendor/autoload.php';
use Automattic\WooCommerce\Client;

//API Woocommerce : https://github.com/woocommerce/woocommerce/wiki/Getting-started-with-the-REST-API
$woocommerce = new Client(
    'https://myshop.com', 
    'ck_xxx', 
    'cs_xxx'
);

//API REST Hiboutik : https://www.logiciel-caisse-gratuit.com/api-rest-hiboutik-activation/
$hiboutik_account = "accounthiboutik";
$user = "contact@myshop.com";
$pass = "xxx";

$le_message_retour = "";

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

if (!isset($_POST['product_id'])) {print("Please provide a valid product_id");exit;} else {$product_id = $_POST['product_id'];}

$result = $hiboutik->get("/products/$product_id");
if ($hiboutik->request_ok) {

//on récupère les infos relatives au produit
$product_model = $result[0]['product_model'];
$product_barcode = $result[0]['product_barcode'];
$product_price = $result[0]['product_price'];

//on va rechercher le produit a modifier sur le woocommerce
$parameters = array();
$products_woocommerce = $woocommerce->get("products/?sku=$product_barcode", $parameters);
foreach($products_woocommerce as $product_woocommerce)
{
$my_id_product_woocommerce = $product_woocommerce->id;

$data = array(
  "name" => $product_model
);

//mise à jour du produit
$update_stock = $woocommerce->put("products/$my_id_product_woocommerce", $data);
$le_message_retour = "Produit $my_id_product_woocommerce modifié sur Woocommerce";
}



}
else
{
$le_message_retour = "Connexion error";
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

