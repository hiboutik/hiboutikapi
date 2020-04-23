<?php
/**
 *
 *  send_image_product.php
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet d'envoyer en masse les images produits
 *
 * Le script va :
 * - lister les images du réportoire images
 * - pour chaque image en fonction de son nom vérifier si un produits correspond dans la base Hiboutik (soit sur la ref fournisseur soit sur le code barre)
 * - envoyer la photo pour la miniature
 * - envoyer la photo en 1000x1000
 *
 */



error_reporting(E_ALL);

require __DIR__.'/../../HiboutikAPI/src/Hiboutik/HiboutikAPI/autoloader.php';

//API REST Hiboutik : https://www.logiciel-caisse-gratuit.com/api-rest-hiboutik-activation/
$hiboutik_account = "";
$user = "";
$pass = "";

$directory = "images";

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

$files = scandir($directory);

foreach($files as $file) {
//on récupère l'extension du fichier et son nom
$extension = mb_strtolower(substr($file, strrpos($file, '.') + 1));
$file_name = substr($file, 0, strrpos($file, '.'));

if ($extension == "jpg" OR $extension == "jpeg" OR $extension == "png")
{
print("<br />Image $file");
$search_product = $hiboutik->get("/products/search/supplier_reference/$file_name/");  //Pour la recherche par référence fournisseur
//$search_product = $hiboutik->get("/products/search/barcode/$file_name/"); //Pour la recherche par code barre

if ($hiboutik->request_ok) {
$id_prod = $search_product[0]['product_id'];

print(" --> $id_prod");

$send_img = $hiboutik->post("/products_images/$id_prod/", array('framing_type' => 'frame'), [
  'image' => [
    [
      'file' => "images/$file"
    ]
  ]
]);

$$send_img = $hiboutik->post("/products_images_1000x1000/$id_prod/", array('framing_type' => 'frame', 'image_id' => 1), [
  'image' => [
    [
      'file' => "images/$file"
    ]
  ]
]);

}

}

}

