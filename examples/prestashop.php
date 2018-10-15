<?php
/**
 * Sync Prestashop
 * -----------------------------------------------------------------------------
 */

require 'HiboutikAPI/src/Hiboutik/HiboutikAPI/autoloader.php';

//Base de données de votre Prestashop
$mysql_host = "localhost";
$mysql_user = "";
$mysql_pass = "";
$mysql_db = "prestashop";
$prefix_tables = "pss_";

$connec_mysql_ok = 0;
if ($mysql_user <> "" AND $mysql_pass <> "")
{
$mysqli = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if (mysqli_connect_errno($mysqli)) {
    echo "Echec lors de la connexion à MySQL : " . mysqli_connect_error();
}
$mysqli->set_charset("utf8");
$connec_mysql_ok = 1;
}

//API REST Hiboutik
$hiboutik_account = '';
$user = '';
$pass = '';

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

$result = $hiboutik->get("/stock_available/warehouse_id/1");
if ($hiboutik->request_ok) {
//  print_r($result);

foreach ($result as $valeur)
{
$stock_available = $valeur[stock_available];
$product_barcode = $valeur[product_barcode];

$sql = "UPDATE " . $prefix_tables . "stock_available, " . $prefix_tables . "product SET " . $prefix_tables . "stock_available.quantity = '$stock_available' WHERE " . $prefix_tables . "stock_available.id_product = " . $prefix_tables . "product.id_product AND " . $prefix_tables . "product.ean13 = '$product_barcode';";
if ($connec_mysql_ok == "1") $mysql_query = mysqli_query ($mysqli, $sql);

$sql = "UPDATE " . $prefix_tables . "stock_available, " . $prefix_tables . "product_attribute SET " . $prefix_tables . "stock_available.quantity = '$stock_available' WHERE " . $prefix_tables . "stock_available.id_product = " . $prefix_tables . "product_attribute.id_product AND " . $prefix_tables . "product_attribute.ean13 = '$product_barcode' AND " . $prefix_tables . "product_attribute.id_product_attribute = " . $prefix_tables . "stock_available.id_product_attribute;";
if ($connec_mysql_ok == "1") $mysql_query = mysqli_query ($mysqli, $sql);
}

} else {
  print 'An error has occured';
  if (isset($result['details']['error_description'])) {
    print ': '.$result['details']['error_description'];
  } else {
    print ': '.$result['error_description'];
  }
}
