<?php
/**
 * Basic exemple; authentication with OAuth
 * -----------------------------------------------------------------------------
 */

require 'HiboutikAPI/src/Hiboutik/autoloader.php';


$hiboutik_account = 'my_account';// https://my_account.hiboutik.com
$access_token = '2c88e36c68f87a0186r88d370cbb2b4e824f270f';

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account);
$hiboutik->oauth($access_token);

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
