# PHP library for Hiboutik API


This repository contains the open source PHP library that allows you to access the **[Hiboutik](https://www.hiboutik.com)** Platform from your PHP app.



## Requirements

* PHP 5.3.0 or newer
* Composer
* cURL library for PHP
* Your Hiboutik access token: https://www.hiboutik.com



## Installation

The Hiboutik PHP library can be installed with Composer. Run this command:


```sh
composer require hiboutik/hiboutikapi
```

And in your script

```php
<?php
require 'vendor/autoload.php';
```

## Manual installation
Download the Curl class: [php-curl-class](https://github.com/php-curl-class/php-curl-class/tree/master/src/Curl) with its three files. Include them in your file and then the HiboutikAPI class.

```php
<?php
include "CaseInsensitiveArray.php";
include "Curl.php";
include "MultiCurl.php";

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
