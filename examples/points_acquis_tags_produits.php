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
 *   Ce script permet de d'identifier les points acquis par client en fonction des tags produits
 *
 */



require __DIR__.'/../../HiboutikAPI/src/Hiboutik/HiboutikAPI/autoloader.php';

//API REST Hiboutik
$hiboutik_account = "";
$user = "";
$pass = "";


$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);



try {

if (isset($_POST['customer_id']))
{
$customer_id = $_POST['customer_id'];
}
elseif (isset($_POST['sale_id']))
{
$infos_vente = $hiboutik->get("/sales/".$_POST['sale_id']."/");
$customer_id = $infos_vente[0]['customer_id'];
}
else
{
throw new Exception("No sale_id or customer_id received");
}


//récupératoin des tags produits
$tags_products = $hiboutik->get("/tags/products/");
$tags_produits = array();
foreach ($tags_products as $tags_product) {
foreach ($tags_product[tag_details] as $key => $value) $tags_produits[$value['tag_id']]['label'] = $value['tag'];
}

foreach ($tags_produits as $key => $value) {
$tags_products = $hiboutik->get("/products/search/tags/$key");
$tags_produits[$key]['points'] = 0;
$tags_produits[$key]['prods'] = $tags_products;
}


//récupératoin des produits achetés par le client
$products_solds = $hiboutik->get("/customer/$customer_id/products_solds/");
foreach ($products_solds as $key => $value) {
foreach ($tags_produits as $cle => $valeur) {
foreach ($valeur['prods'] as $cl => $val) {
if ($val['product_id'] == $value['product_id']) $tags_produits[$cle]['points'] = $tags_produits[$cle]['points'] + $value['points'];
}
}
}

//on affiche les résultats
$message = "Points par tag produit";
foreach ($tags_produits as $key => $value) {
$message .= "<br />".$value['label']." : ".$value['points']." points";
}

header('Content-type: application/json; charset=utf-8');
$message_retour["alerte"] = "
<div class=\"alert alert-info alert-dismissable\">
<button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
<strong>$message</strong>
</div>
";
echo json_encode($message_retour);


}
catch (Exception $e)
{
$message_erreur = $e->getMessage();
header('Content-type: application/json; charset=utf-8');
$message_retour["alerte"] = "
<div class=\"alert alert-danger alert-dismissable\">
<button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
<strong>$message_erreur</strong>
</div>
";
echo json_encode($message_retour);
}

exit();


