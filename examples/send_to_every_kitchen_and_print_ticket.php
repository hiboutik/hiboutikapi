<?php
/**
 *
 *  HiPay
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet d'envoyer la totalité d'une table en cuisine sur les écrans puis d'imprimer sur une imprimante thermique un ticket récapitulatif.
 *
 */



require __DIR__.'/../../HiboutikAPI/src/Hiboutik/HiboutikAPI/autoloader.php';

//API REST Hiboutik
$hiboutik_account = "";
$user = "";
$pass = "";

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

if (!isset($_GET['sale_id'])) {print("Please provide a valid sale_id");exit;} else {$sale_id = $_GET['sale_id'];}

$result = $hiboutik->get("/sales/$sale_id");
if ($hiboutik->request_ok) {
//print_r($result);

//on initialise les dnnées a imprimer
$doc_a_imprimer = "
<hibou_double_width++>\r
Ticket cuisine
</hibou_double_width>\r


<hibou_bold>\r
######################################
Vente ".$result[0][sale_id]."
Table ".$result[0][ressource_name]."
######################################
</hibou_bold>\r

";


//on récupère tous les produits de la vente
$line_items = $result[0]['line_items'];

//pour chaque ligne de la vente
foreach ($line_items as $valeur)
{
$line_item_id = $valeur[line_item_id];

//on balance en cuisine
$envoi_cuisine = $hiboutik->post("/kitchen/line_item", ['item_id' => $line_item_id]);

//on prépare le document a imprimer
$doc_a_imprimer .= "
x".$valeur[quantity]." ".$valeur[product_model];

//si il y a une cuisson alors on l'imprime
if ($valeur[product_size] <> "0") $doc_a_imprimer .= " --> ".$valeur[size_name];

//si il y a des options alors on les imprime
if (!empty($valeur[modifiers]))
{
$doc_a_imprimer .= "
Options : ";
foreach ($valeur[modifiers] as $valeurm) $doc_a_imprimer .= " ".$valeurm[modifier_label];
}
}

//enfin on envoie a l'imprimante
$print = $hiboutik->post("/print/misc/", [
'store_id' => $result[0]['store_id'],
'data' => $doc_a_imprimer
]);
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
