<?php
/*
####################################################################################################
//Constantes à adapter selon votre compte Hiboutik
####################################################################################################
*/


//API REST Hiboutik
$account = "";
$user = "";
$key = "";


/*
####################################################################################################
//Variables
####################################################################################################
*/

$url = "https://$account.hiboutik.com/apirest";
$ma_date = date("Y-m-d H:i:s");
$maintenant = time();
$ma_date_jour = date("Ymd", $maintenant);
$url_account = preg_replace('/apirest/', '', $url);

$az01 = "abcdefghijklmnopqrstuvwxyz0123456789";
$size = 8;

/*
####################################################################################################
//Connexion à l'API Hiboutik
####################################################################################################
*/
require 'vendor/autoload.php';
$hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $key);


/*
####################################################################################################
//Récupération des utilisateurs & envoi du mail avec le nouveau mot de passe
####################################################################################################
*/
$users = $hiboutik->getHiboutik("users");
foreach ($users as $key => $value) {
$my_user_id = $value -> user_id;
$my_user_last_name = $value -> user_last_name;
$my_user_first_name = $value -> user_first_name;
$my_user_email = $value -> user_email;
$my_user_validity = $value -> user_validity;
$my_user_validity = preg_replace('/-/', '', $my_user_validity);
if ($my_user_validity >= $ma_date_jour)
{

/*
####################################################################################################
//Création du nouveau mot de passe
####################################################################################################
*/
$new_pass = "";
srand((double)microtime()*date("YmdGis"));
for($cnt = 0; $cnt < $size; $cnt++) $new_pass .= $az01[rand(0, 35)];

/*
####################################################################################################
//Enregistrement du nouveau mot de passe
####################################################################################################
*/
$data = array(
  "user_id" => "$my_user_id",
  "new_password" => "$new_pass",
);
$update_user_pwd = $hiboutik->putHiboutik("user/password", $data);


/*
####################################################################################################
//Envoi du mail
####################################################################################################
*/
mail("$my_user_email", "Acces Hiboutik", "\nBonjour $my_user_first_name $my_user_last_name,\n\n\nVoici ton nouveau mot de passe pour acceder à Hiboutik : $new_pass\n\na+\n\nLe Hibou", "From: $user");

}
}




?>
