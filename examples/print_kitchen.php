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
 *   Ce script permet d'envoyer les plats a préparer sur les imprimantes en cuisine
 *   Il faut au préalable créer une redirection de port pour chaque imprimante sur votre routeur/box
 *   Ce script envoie en simultané sur toutes les imprimantes de cuisine
 *
 */
 
 
// Set your Hiboutik credentials here
$account = "xxx";
$user = "contact@hiboutik.com";
$key = "xxx";
$webhook_secret = "xxx";

$hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $key);


/*
Il faut activer les tags produits : https://faq.hiboutik.com/?faq=70
Ces tags vont permettre de créer les imprimantes
Créez une catégorie de tags puis dans les tags donnez les noms de vos imprimantes.
Dans le description du tag il faut indiquer le port de l'imprimante (cf. infra redirection de port)
*/
$tag_imprimantes_cuisine = 17; //ici l'identifiant de la catégorie de tags dans laquelle vous avez créé vos imprimantes


try {

//on vérifie l'authenticité du webhook grâce au webhook_secret
$payload = file_get_contents('php://input');
$hash = hash_hmac('SHA256', $payload, $webhook_secret);
if (
  !isset($_SERVER['HTTP_X_HIBOUTIK_HMAC_SHA256'])
  or !hash_equals($hash, $_SERVER['HTTP_X_HIBOUTIK_HMAC_SHA256'])
) {
  header('HTTP/1.1 401 Unauthorized');
  exit;
}

if (!isset($_GET['sale_id'])) {throw new Exception("No sale_id received");} else {$sale_id = $_GET['sale_id'];}
if (!isset($_POST['ip_addr'])) {throw new Exception("No ip_addr received");} else {$ip_address = $_POST['ip_addr'];}

//récupération des tags imprimantes
$tags = $hiboutik->get("/tags/products/");
$les_imprimantes = array();
foreach ($tags AS $my_tag_cat) if ($my_tag_cat['tag_cat_id'] == $tag_imprimantes_cuisine) foreach ($my_tag_cat['tag_details'] AS $my_tag) $les_imprimantes[$my_tag['tag_id']] = $my_tag['tag_desc'];


$sale_infos = $hiboutik->get("/sales/$sale_id");

$doc_a_imprimer = array();

//on initialise les dnnées a imprimer
$modele_doc_a_imprimer = "

<hibou_use_font_a>\r
<hibou_bold>\r
<hibou_font_size>2|2\r
-------------------------
".$sale_infos[0]['ressource_name']." - ".$sale_infos[0]['guests_number']."

".$sale_infos[0]['vendor_first_name']." ".$sale_infos[0]['vendor_last_name']." - ".date("d/m H:i")."
-------------------------
</hibou_bold>\r
<hibou_font_size>2|2\r
";


//on récupère tous les produits de la vente
$line_items = $sale_infos[0]['line_items'];

//on récupère les éléments de la cuisine
$deja_envoye_en_cuisine = array();
$line_items_cuisine = $hiboutik->get("/kitchen/sales/$sale_id/");
foreach ($line_items_cuisine[0]['line_items'] as $mon_line_item)
{
$deja_envoye_en_cuisine[] = $mon_line_item['item_id'];
}



//pour chaque ligne de la vente
foreach ($line_items as $valeur)
{
$line_item_id = $valeur['line_item_id'];

//on récupère les tags du produit
foreach ($valeur['tags'] as $my_tag)
{
if ($my_tag['tag_cat'] == $tag_imprimantes_cuisine)
{
$le_port = $les_imprimantes[$my_tag['tag_id']];

if (!in_array($line_item_id, $deja_envoye_en_cuisine))
{
if (!isset($doc_a_imprimer[$le_port])) $doc_a_imprimer[$le_port] = $modele_doc_a_imprimer;

//on prépare le document a imprimer
$doc_a_imprimer[$le_port] .= "

".$valeur['quantity']." ".$valeur['product_model']."";

//si il y a une cuisson alors on l'imprime
if ($valeur['product_size'] <> "0") $doc_a_imprimer[$le_port] .= " + ".$valeur['size_name'];

//si il y a des options alors on les imprime
if (!empty($valeur['modifiers']))
{
$doc_a_imprimer[$le_port] .= "
";
foreach ($valeur['modifiers'] as $valeurm) $doc_a_imprimer[$le_port] .= " ".$valeurm['modifier_label'];
}
//on envoie tout en cuisine
$kitchen = $hiboutik->post("/kitchen/line_item/", [
'item_id' => $line_item_id
]);

}

}
}


}


foreach ($doc_a_imprimer as $cle => $valeur) {
//enfin on envoie a l'imprimante
$print = $hiboutik->post("/print/misc/", [
'store_id' => $sale_infos[0]['store_id'],
'port' => $cle,
'ip_address' => $ip_address,
'data' => $valeur
]);
}


header('Content-type: application/json; charset=utf-8');
$message_retour["alerte"] = "
<div class=\"alert alert-info alert-dismissable\">
<button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
<strong>Envoyé en cuisine</strong>
</div>
";
echo json_encode($message_retour);
/*
*/



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

