<?php
/**
 * Basic exemple; basic authentication
 * -----------------------------------------------------------------------------
 */

require 'HiboutikAPI/src/Hiboutik/autoloader.php';


$hiboutik_account = 'my_account';// https://my_account.hiboutik.com
$user = 'user@exemple.com';
$pass = 'fih6874Dgsd8fgsUIGHsdg';

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

$result = $hiboutik->get("/brands/");
if ($hiboutik->request_ok) {
  print_r($result);
  if (!empty($pagination = $hiboutik->pagination())) {
    print_r($pagination);
  }
} else {
  print 'An error has occured';
  if (isset($result['details']['error_description'])) {
    print ': '.$result['details']['error_description'];
  } else {
    print ': '.$result['error_description'];
  }
}
