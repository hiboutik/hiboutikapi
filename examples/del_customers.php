<?php
/**
 *
 *  Running conseil
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet de supprimer les clients 
 *   Il suffit d'indiquer le premier client a supprimer (customer_deb) ainsi que le dernier (customer_fin)
 *
 */



require __DIR__.'/../../HiboutikAPI/src/Hiboutik/HiboutikAPI/autoloader.php';

//API REST Hiboutik
$hiboutik_account = "";
$user = "";
$pass = "";

$customer_deb = 1;
$customer_fin = 999;


$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

try {


for ($i = $customer_deb; $i <= $customer_fin; $i++)
{
$del_customer = $hiboutik->delete("/customer/$i");
print_r($del_customer);
}



}
catch (Exception $e)
{
$message_erreur = $e->getMessage();
header('Content-type: text/html; charset=utf-8');
echo ($message_erreur);
}

exit();


