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
 *   Ce script permet d'envoyer au TPE HiPay le montant a encaisser. Le script, lorsqu'il est appelé à partir d'un bouton action d'une page vente, va interroger l'API Hiboutik pour avoir le détails des modes de paiement utilisés et va ensuite afficher le/s bouton/s permettant l'envoi au TPE HiPay via l'API HiPay. Il faut penser a whitelister l'adresse IP de la machine qui effectue la requête sur l'interface HiPay.
 *
 */

require 'HiboutikAPI/src/Hiboutik/HiboutikAPI/autoloader.php';

//API REST Hiboutik
$hiboutik_account = "";
$user = "";
$pass = "";

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

if (!isset($_GET['sale_id'])) {print("Please provide a valid sale_id");exit;} else {$sale_id = $_GET['sale_id'];}

$page = "https://".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];

//on récupère les informations relatives a la vente
$result = $hiboutik->get("/sales/$sale_id");
if ($hiboutik->request_ok) {
//print_r($result);

$currency = $result[0]['currency'];
$payment = $result[0]['payment'];


if (isset($_POST['action']) AND $_POST['action'] == "envoyer_paiement")
{
//récupération du vendor_id - Utile si plusieurs TPE 
$vendor_id = $result[0]['vendor_id'];

$url_prod_hipay = "https://secure-gateway.hipay-tpp.com/rest/v1/order";
$payment_terminal_id = "";
$cle_hipay = "";
$token_hipay = "";


$montant = $_POST['montant'];

$timestamp_hipay = date("YmdHis")."_".$sale_id;
$token_key_hipay = $token_hipay.":".$cle_hipay;

$fields = [
    'orderid' => $timestamp_hipay,
    'eci' => 10,
    'description' => $sale_id,
    'currency' => "$currency",
    'amount' => $montant,
    'language' => 'fr_FR',
    'initialize_payment_terminal' => 1,
    'pos_transaction_lifetime' => 180,
    'payment_terminal_id' => $payment_terminal_id
];

$curl_hipay = curl_init($url_prod_hipay);
curl_setopt ($curl_hipay, CURLOPT_POST, 1);
curl_setopt($curl_hipay, CURLOPT_USERPWD,  $token_key_hipay);
curl_setopt ($curl_hipay, CURLOPT_POSTFIELDS, $fields);
curl_setopt($curl_hipay, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
curl_setopt( $curl_hipay, CURLOPT_RETURNTRANSFER, 1);

$response_hipay = curl_exec( $curl_hipay );
curl_close($curl_hipay);

print_r($response_hipay);
//traitement du retour
}


//cas où il y a un seul moyen de paiement
if ($payment == "CB")
{
$total = $result[0]['total'];
print("
<form action=\"$page\" method=\"post\" target=\"_self\">
  <input type=\"hidden\" name=\"action\" value=\"envoyer_paiement\">
  <input type=\"hidden\" name=\"montant\" value=\"$total\">
  <button type=\"submit\">Envoyer $currency$total a HiPay</button>
</form>
");
}
//cas où il y au moins deux moyens de paiement
elseif ($payment == "DIV")
{
foreach ($result[0]['payment_details'] as $valeur)
{
$payment_amount = $valeur['payment_amount'];
print("
<form action=\"$page\" method=\"post\" target=\"_self\">
  <input type=\"hidden\" name=\"action\" value=\"envoyer_paiement\">
  <input type=\"hidden\" name=\"montant\" value=\"$payment_amount\">
  <button type=\"submit\">Envoyer $currency$payment_amount a HiPay</button>
</form>
");
}
}

print("
<form action=\"https://$hiboutik_account.hiboutik.com/\" method=\"post\" target=\"_top\">
  <input type=\"hidden\" name=\"ma_commande_affiche\" value=\"$sale_id\">
  <button type=\"submit\">Retour a la vente</button>
</form>
");

} else {
print 'An error has occured';
if (isset($result['details']['error_description'])) {
print ': '.$result['details']['error_description'];
} else {
print ': '.$result['error_description'];
}
}
