# PHP library for Hiboutik API


This repository contains the open source PHP library that allows you to access the **[Hiboutik](https://www.hiboutik.com)** Platform from your PHP app.



## Requirements

* PHP 5.3.0 or newer
* Composer
* cURL library for PHP (php-curl-class)
* Your Hiboutik access token: https://www.hiboutik.com



## Installation

The Hiboutik PHP library can be automatically installed with Composer or manually.

### Composer

Run this command:
```sh
composer require hiboutik/hiboutikapi
```

And in your script

```php
<?php
require 'vendor/autoload.php';
```

### Manual installation
Download the Curl class: [php-curl-class](https://github.com/php-curl-class/php-curl-class/tree/master/src/Curl) (latest version as of this writing: 7.4.0).
Include the files in your script and then the HiboutikAPI class.

```php
<?php
require 'php-curl-class-master/src/Curl/ArrayUtil.php';rray.php";
require 'php-curl-class-master/src/Curl/Curl.php';
require 'php-curl-class-master/src/Curl/CaseInsensitiveArray.php';
require 'php-curl-class-master/src/Curl/Decoder.php';
require 'php-curl-class-master/src/Curl/MultiCurl.php';
require 'php-curl-class-master/src/Curl/StrUtil.php';
require 'php-curl-class-master/src/Curl/Url.php';

include "HiboutikAPI.php"
```

## Quick Documentation

Initialize Hiboutik API with your API credentials:
```php
// Get your access token here: https://www.hiboutik.com/
$hiboutik = new \Hiboutik\HiboutikAPI(YOUR_HIBOUTIK_ACCOUNT, USER, KEY);
```



## Usage

To list all active products on your account:
```php
$products = $hiboutik->getHiboutik("products");
if ($products !== NULL) {
  // Do stuff
} else {// An error occured
  switch ($hiboutik->errorCode) {
    case 401:
      // Unauthorized
      print $hiboutik->errorMessage;
      break;
    case 500:
      // Server error
      print $hiboutik->errorMessage;
      break;
    default:
      // Unknown error
  }
}
```
Returns an array of `Products` objects.

To create a new product:
```php
$data = [
  "product_model" => "My product",
  "product_barcode" => "",
  "product_brand" => "2",
  "product_supplier" => "2",
  "product_price" => "99.90",
  "product_discount_price" => "89.90",
  "product_category" => 5,
  "product_size_type" => 0,
  "product_stock_management" => 0,
  "product_supplier_reference" => "",
  "product_vat" => 0
];
$hiboutik->postHiboutik("products", $data);
```



## License

Please see the [license file](https://github.com/hiboutik/hiboutikapi/blob/master/LICENSE) for more information.
