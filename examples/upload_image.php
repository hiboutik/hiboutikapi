<?php
/**
 * Upload an image
 * -----------------------------------------------------------------------------
 */

require 'HiboutikAPI/src/Hiboutik/autoloader.php';


$hiboutik_account = 'my_account';// https://my_account.hiboutik.com
$user = 'user@exemple.com';
$pass = 'fih6874Dgsd8fgsUIGHsdg';

$hiboutik = new \Hiboutik\HiboutikAPI($hiboutik_account, $user, $pass);

$result = $hiboutik->post('/products_images/2/', null, [
  'image' => [
    [
      'file' => '/path/to/image.jpg'
    ]
  ]
]);
if ($hiboutik->request_ok) {
  print_r($result);
} else {
  print 'An error has occured';
  if (isset($result['details']['error_description'])) {
    print ': '.$result['details']['error_description'];
  } else {
    print ': '.$result['error_description'];
  }
}
