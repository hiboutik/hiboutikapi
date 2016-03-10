<?
/*
####################################################################################################
//Constantes à adapter selon votre compte Hiboutik
####################################################################################################
*/

require 'vendor/autoload.php';

//API REST Hiboutik
$account = "";
$user = "";
$key = "";


$url = "https://$account.hiboutik.com/apirest";
$ma_date = date("Y-m-d H:i:s");
$maintenant = time();
$ma_date_jour = date("Ymd", $maintenant);
$url_account = preg_replace('/apirest/', '', $url);


/*
####################################################################################################
//Connexion à l'API Hiboutik
####################################################################################################
*/
$hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $key);


//récupération des produits
$products = $hiboutik->getHiboutik("products");
foreach ($products as $key => $value) {
$my_product_id = $value -> product_id;
$my_product_name[$my_product_id] = $value -> product_model;
$my_product_category[$my_product_id] = $value -> product_category;
$my_product_size_type[$my_product_id] = $value -> product_size_type;
$my_product_product_stock_management[$my_product_id] = $value -> product_stock_management;
}

//récupération des types de tailles et des tailles
$size_types = $hiboutik->getHiboutik("size_types");
foreach ($size_types as $key => $value) {
$my_size_type_id = $value -> size_type_id;
$sizes = $hiboutik->getHiboutik("sizes/$my_size_type_id");
foreach ($sizes as $key_size => $value_size) {
$my_size_id = $value_size -> size_id;
$my_size_name[$my_size_type_id][$my_size_id] = $value_size -> size_name;
}
}


//récupération des entrepôts
$warehouses = $hiboutik->getHiboutik("warehouses");
foreach ($warehouses as $key => $value) {
$warehouse_id = $value -> warehouse_id;
$my_warehouse_name[$warehouse_id] = $value -> warehouse_name;
}

//récupération des catégories
$categories = $hiboutik->getHiboutik("categories");
foreach ($categories as $key => $value) {
$category_id = $value -> category_id;
$my_category_name[$category_id] = $value -> category_name;
}


print("
<!doctype html>
<html lang=\"en\">
<head>
<meta charset=\"utf-8\">
<title>Hiboutik - $account</title>

<link  href=\"https://static.hiboutik.com/lib/jquery-ui-1.11.4/jquery-ui.min.css?v=2\" rel=\"stylesheet\" type=\"text/css\"/>
<script src=\"https://static.hiboutik.com/lib/jquery-2.1.3/jquery-2.1.3.min.js\" type=\"text/javascript\"></script>
<script src=\"https://static.hiboutik.com/lib/jquery-ui-1.11.4/jquery-ui.min.js\" type=\"text/javascript\"></script>


<link  href=\"https://static.hiboutik.com/lib/jquery-tablesorter/themes/blue/style.css\" rel=\"stylesheet\" type=\"text/css\"/>
<script src=\"https://static.hiboutik.com/lib/jquery-tablesorter/jquery.tablesorter.min.js\" type=\"text/javascript\"></script>

<script>
\$(function() {
\$( \"#accordion\" ).accordion({
collapsible: true
});
});

\$(function() {
\$( \"input[type=submit], a, button\" )
  .button()
});

$(document).ready(function() { 
    // call the tablesorter plugin 
    $(\"table\").tablesorter({ 
        // sort on the first column and third column, order asc 
        sortList: [[0,0],[2,0]] 
    }); 
}); 

</script>
</head>
<body>
<div id=\"accordion\">
");

for ($i = 1; $i <= $warehouse_id; $i++)
{
$cumul_produits_vendus = array();
$cumul_produits_vendus_7jours = array();

//récupération de ce qui a été vendu sur les 7 derniers jours
for ($j = 0; $j < 7; $j++)
{
//calcul de la date
$date_calcul = $maintenant - ($j * 24 * 60 * 60);
$annee = date("Y", $date_calcul);
$mois = date("m", $date_calcul);
$jour = date("d", $date_calcul);
$products_sold = $hiboutik->getHiboutik("products_sold/$i/$annee/$mois/$jour");
foreach ($products_sold as $key => $value) {
$my_product_id = $value -> product_id;
$my_product_size = $value -> product_size;
$my_quantity = $value -> quantity;
$cumul_produits_vendus[$my_product_id][$my_product_size] = $cumul_produits_vendus[$my_product_id][$my_product_size] + $my_quantity;
}
}
$cumul_produits_vendus_7jours = $cumul_produits_vendus;


//récupération de ce qui a été vendu sur les 15 derniers jours
for ($j = 7; $j < 15; $j++)
{
//calcul de la date
$date_calcul = $maintenant - ($j * 24 * 60 * 60);
$annee = date("Y", $date_calcul);
$mois = date("m", $date_calcul);
$jour = date("d", $date_calcul);
$products_sold = $hiboutik->getHiboutik("products_sold/$i/$annee/$mois/$jour");
foreach ($products_sold as $key => $value) {
$my_product_id = $value -> product_id;
$my_product_size = $value -> product_size;
$my_quantity = $value -> quantity;
$cumul_produits_vendus[$my_product_id][$my_product_size] = $cumul_produits_vendus[$my_product_id][$my_product_size] + $my_quantity;
}
}



//récupération du stock disponible
$stock_dispo = array();
$stock_alerte = array();
$stock_available = $hiboutik->getHiboutik("stock_available/warehouse_id/$i");
foreach ($stock_available as $key => $value) {
$id_prod_sa = $value -> product_id;
$id_prod_taille_sa = $value -> product_size;
$id_prod_inventory_alert = $value -> inventory_alert;
$stock_dispo[$id_prod_sa][$id_prod_taille_sa] = $value -> stock_available;
$stock_alerte[$id_prod_sa][$id_prod_taille_sa] = $value -> inventory_alert;
}


if (!empty($cumul_produits_vendus))
{
print("
<h3>$my_warehouse_name[$i]</h3>
<div>

<table class=\"tablesorter\" >
<thead>
<tr>
<th>id</th>
<th>Produit</th>
<th>Catégorie</th>
<th>id_taille</th>
<th>Taille</th>
<th>Vendus j-15</th>
<th>Vendus j-7</th>
<th>Stock</th>
<th>Alerte</th>
<th>Ecart</th>
</tr>
</thead>
<tbody>
");
foreach ($cumul_produits_vendus as $key => $value) {
$my_product_id = $key;
foreach ($value as $key2 => $value2) {
$my_product_size = $key2;
$my_product_size_type_fin = $my_product_size_type[$my_product_id];
$my_quantity_7 = $cumul_produits_vendus_7jours[$my_product_id][$my_product_size];
$my_quantity = $cumul_produits_vendus[$my_product_id][$my_product_size];
$my_product_size_libelle = $my_size_name[$my_product_size_type_fin][$my_product_size];
$my_stock_dispo = $stock_dispo[$my_product_id][$my_product_size];
$my_alerte_stock = $stock_alerte[$my_product_id][$my_product_size];

$my_ecart_stock = 0;
if ($my_alerte_stock <> "0") $my_ecart_stock = $my_stock_dispo - $my_alerte_stock;

$my_product_category_aff = $my_product_category[$my_product_id];
$my_product_category_aff = $my_category_name[$my_product_category_aff];

$my_class_color = "";
if ($my_stock_dispo <= $my_alerte_stock) $my_class_color = " style=\"background-color: red;\"";

if ($my_product_id > "0" AND $my_product_product_stock_management[$my_product_id])
{
print("
<tr>
<td>$my_product_id</td>
<td>
<form method=\"POST\" action=\"$url_account\" target=\"hibou_$my_product_id\" >
<input type=\"hidden\" name=\"mon_produit_affiche\" value=\"$my_product_id\" />
<input type=\"hidden\" name=\"show_tab\" value=\"produits\" />
<input type=\"submit\" value=\"$my_product_name[$my_product_id]\" />
</form>
</td>
<td>$my_product_category_aff</td>
<td>$my_product_size</td>
<td>$my_product_size_libelle</td>
<td>$my_quantity</td>
<td>$my_quantity_7</td>
<td>$my_stock_dispo</td>
<td$my_class_color>$my_alerte_stock</td>
<td$my_class_color>$my_ecart_stock</td>
</tr>
");
}
}
}
print("
</tbody>
</table>
</div>
");

}

}

print("
</div>
</body>
</html>
");



?>
