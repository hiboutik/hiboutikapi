# PHP library for Hiboutik API

This repository contains the open source PHP library that allows you to access the **[Hiboutik](https://www.hiboutik.com)** Platform from your PHP app.

## Requirements

* PHP 5.3.0 or newer
* PHP cURL extension

## Installation

### Composer
The Hiboutik PHP library can be installed with Composer. Run this command:

```sh
composer require hiboutik/hiboutikapi
```

And in your script

```php
<?php
require 'vendor/autoload.php';
```

### Manual installation
Download this package and include the autoloader.
```php
<?php

require 'HiboutikAPI/src/Hiboutik/autoloader.php';

```

## Quick Documentation

There are two types of authentication available: basic and OAuth.

#### Basic authentication

```php
$hiboutik = new \Hiboutik\HiboutikAPI(YOUR_HIBOUTIK_ACCOUNT, USER, KEY);

```

#### OAuth

```php
$hiboutik = new \Hiboutik\HiboutikAPI(YOUR_HIBOUTIK_ACCOUNT);
$hiboutik->oauth(ACCESS_TOKEN);

```

### Usage

To list all active products on your account:
```php
$result = $hiboutik->get("/products/");
if ($hiboutik->request_ok) {
  print_r($result);
} else {
  if (isset($result['details']['error_description'])) {
    print $result['details']['error_description'];
  } else {
    print $result['error_description'];
  }
}

```

To create a new product:
```php
$result = $hiboutik->post("products", [
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
]);

if ($hiboutik->request_ok) {
  print 'Product created!';
} else {
  if (isset($result['details']['error_description'])) {
    print $result['details']['error_description'];
  } else {
    print $result['error_description'];
  }
}

```

### Pagination

The large datasets are paginated in the Hiboutik's API.
Get the pagination information:
```php
$pagination = $hiboutik->pagination();

```

### Legacy support

For the scripts using the previous version of this package the old methods are still available.
The only difference is in the addition of a parameter in the constructor. The API version must be specified for the v1:
```php
$hiboutik = new \Hiboutik\HiboutikAPI(YOUR_HIBOUTIK_ACCOUNT, USER, KEY, '1');
```
