<?php
/**
 *
 *  Franchises
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet de récupérer les données d'un point de vente
 *
 */



require __DIR__.'/../../HiboutikAPI/src/Hiboutik/HiboutikAPI/autoloader.php';

//API REST Hiboutik
$hiboutik_account = "xxxxx"; //libellé du compte
$user = "xxxxx"; //adresse mail
$pass = "xxxxx"; //clé API

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);


$mois = date ("m");
$annee = date ("Y");

try {

//on récupère les points de vente
$stores = $hiboutik->get("/stores");

if ($hiboutik->request_ok) {
//pour chaque point de vente
foreach($stores as $store) 
{
$store_name = $store['store_name'];
$store_id = $store['store_id'];

print("<h1>$store_name</h1>");

$turnover = $hiboutik->get("/reports/turnover/$store_id/$annee/$mois");

print("
<!-- Table Striped Rows -->
<caption>CA par jour</caption>
<div class=\"table-responsive\">
  <table class=\"table table-striped\" border=\"1\">
    <thead>
      <tr>
        <th>Jour</th>
        <th>Jour/semaine</th>
        <th>CA TTC</th>
        <th>CA HT</th>
        <th>Marge</th>
        <th>Ventes</th>
        <th>Produits</th>
      </tr>
    </thead>

    <tbody>
");

foreach ($turnover as $value)
{
$marge = "%";
print("
      <tr>
        <td>$value[date]</td>
        <td>$value[day_of_week]</td>
        <td>$value[total_incl_taxes]</td>
        <td>$value[total_excl_taxes]</td>
        <td>$marge</td>
        <td>$value[nb_sales]</td>
        <td>$value[nb_products]</td>
      </tr>

");


}

print("
    </tbody>
  </table>
</div>
<!-- End Table Striped Rows -->
");


}

}


} catch (Exception $e) {
    $message_retour = $e->getMessage();
    echo "Error : $message_retour";
}
