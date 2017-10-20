<?php
/**
 *
 *  Class HiboutikAPI
 *
 *  @version 1.1.0
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 *******************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 *******************************************************************************
 *
 *   This class makes API calls for the Hiboutik API. For a complete
 *   documentation refer to your Hiboutik account:
 *   SETTINGS -> USERS -> Click on the wrench symbol.
 *
 *
 *
 *  @INSTALL:
 *  ---------
 *  You have two choises, with composer or manual.
 *
 *  1. With Composer:
 *
 *  Install in terminal:
 *  <code>
 *    composer require hiboutik/hiboutikapi
 *  </code>
 *
 *  Use it:
 *  <code>
 *    $account = "my_shop";
 *    $user = "my_email@provider.com";
 *    $api_key = "9ECJFMF1QZ183Z1ONO0Z2XLZVTCSA4ZP01T";
 *
 *    $hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $api_key);
 *  </code>
 *
 *  or
 *
 *  2. Manual install:
 *
 *  Download the Curl class (latest version as of this writing: 7.4.0) from
 *  here:
 *  https://github.com/php-curl-class/php-curl-class.
 *  Include the files in your file and then the HiboutikAPI class.
 *
 *  <code>
 *    require 'php-curl-class-master/src/Curl/ArrayUtil.php';
 *    require 'php-curl-class-master/src/Curl/Curl.php';
 *    require 'php-curl-class-master/src/Curl/CaseInsensitiveArray.php';
 *    require 'php-curl-class-master/src/Curl/Decoder.php';
 *    require 'php-curl-class-master/src/Curl/MultiCurl.php';
 *    require 'php-curl-class-master/src/Curl/StrUtil.php';
 *    require 'php-curl-class-master/src/Curl/Url.php';
 *
 *    include "HiboutikAPI.php"
 *
 *
 *    $account = "my_shop";
 *    $user = "my_email@provider.com";
 *    $api_key = "9ECJFMF1QZ183Z1ONO0Z2XLZVTCSA4ZP01T";
 *
 *    $hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $api_key);
 *  </code>
 *
 *
 *
 *
 *  @USAGE:
 *  -------
 *
 *  <code>
 *    $account = "my_shop";//       url: https://my_shop.hiboutik.com/
 *    $user = "user@email.com";//   Hiboutik user name: the email address you
 *    use to connect to your account.
 *    $key = "ORECJF05UQZ133Z1O4O0Z2XLZ7TCSAVZP01Z";//  Hiboutik API token
 *
 *    $hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $key);// Initialize
 *
 *    //  Get all products
 *    $products = $hiboutik->getHiboutik("products");
 *    if ($products !== NULL) {
 *      // Do stuff
 *    } else {// An error occured
 *      switch ($hiboutik->errorCode) {
 *        case 401:
 *          // Unauthorized
 *          print $hiboutik->errorMessage;
 *          break;
 *        case 500:
 *          // Server error
 *          print $hiboutik->errorMessage;
 *          break;
 *        default:
 *          // Unknown error
 *      }
 *    }
 *
 *
 *    //  Create a product
 *    $data = [
 *      "product_model"=> "My product",
 *      "product_barcode"=> "",
 *      "product_brand"=> "My Brand",
 *      "product_supplier"=> "2",
 *      "product_price"=> "99.90",
 *      "product_discount_price"=> 89.90",
 *      "product_category"=> 5,
 *      "product_size_type"=> 0,
 *      "product_stock_management"=> 0,
 *      "product_supplier_reference"=> "",
 *      "product_vat"=> 0
 *    ];
 *    $hiboutik->postHiboutik("products", $data);
 *    if ($products !== NULL) {
 *      // Do stuff
 *    } else {// An error occured
 *      switch ($hiboutik->errorCode) {
 *        case 401:
 *          // Unauthorized
 *          print $hiboutik->errorMessage;
 *          break;
 *        case 500:
 *          // Server error
 *          print $hiboutik->errorMessage;
 *          break;
 *        default:
 *          // Unknown error
 *      }
 *    }
 *  </code>
 *
 *
 *
 *
 *
 *  @METHODS:
 *  ---------
 *
 *  $hiboutik->getHiboutik($rest_resource);
 *             ---------------------------
 *    This method returns an array with the requested data or NULL if an error
 *    occured. For a complete reference of the Hiboutik API see the
 *    documentation via your Hiboutik account.
 *    @param
 *      $rest_resource        : string, required
 *    @return
 *      array if successful
 *      NULL if error
 *
 *  $hiboutik->postHiboutik($rest_resource, $data);
 *             -----------------------------------
 *    This method sends post requests. The $data array must be formatted
 *    according to the Hiboutik API.
 *    @param
 *      $rest_resource        : string, required
 *      $data                 : array, required
 *    @return
 *      string if successful
 *      NULL if error
 *
 *  $hiboutik->putHiboutik($rest_resource, $data);
 *             ----------------------------------
 *    This method sends put requests. The $data array must be formatted
 *    according to the Hiboutik API.
 *    @param
 *      $rest_resource        : string, required
 *      $data                 : array, required
 *    @return
 *      string if successful
 *      NULL if error
 *
 *  $hiboutik->deleteHiboutik($rest_resource, $id[, $id, ...]);
 *             -----------------------------------------------
 *    This method sends delete requests. The $data array must be formatted
 *    according to the Hiboutik API.
 *    @param
 *      $rest_resource        : string, required
 *      $id                   : string, required
 *    @return
 *      string if successful
 *      NULL if error
 *
 *
 *  If an error occured, its details will be available, inherited from the Curl
 *  class:
 *    $hiboutik->errorCode;
 *    $hiboutik->errorMessage;
 *  See php-curl-class documentation for more details.
 *
 */




namespace Hiboutik;
use Curl;


class HiboutikAPI extends \Curl\Curl
{
  const VERSION_API = "1.1.0";

  protected $account_connection;
  protected $user_connection;
  protected $key_connection;



  public function __construct($account, $user, $key)
  {
    try {
      if(count(func_get_args()) !== 3) {
        throw new \Exception("HiboutikAPI: The contructor expects exactly 3 (three) arguments.");
      }

      $this->account_connection = "https://".$account.".hiboutik.com/apirest";
      $this->user_connection    = $user;
      $this->key_connection     = $key;

      parent::__construct();

      $user_agent     = "HiboutikAPI v".self::VERSION_API." (+https://github.com/hiboutik/hiboutikapi)";
      $user_agent    .= " PHP/" . PHP_VERSION;
      $curl_version   = curl_version();
      $user_agent    .= ' curl/' . $curl_version['version'];
      $this->setHeader('Content-Type', 'application/json');
      $this->setBasicAuthentication($user, $key);
      $this->setUserAgent($user_agent);
    } catch(\Exception $e) {
      trigger_error($e->getMessage()." -> ".$e->getTraceAsString(), E_USER_WARNING);
    }
  }




  public function getHiboutik($rest_resource = "")
  {
    try {
      $this->get($this->account_connection."/".$rest_resource);
      if ($this->error) {
        throw new \Exception("CURL error: ".$this->errorCode." : ".$this->errorMessage);
      } else {
        return $this->response;
      }
    } catch(\Exception $e) {
      trigger_error($e->getMessage()." -> ".$e->getTraceAsString(), E_USER_WARNING);
    }
  }




  public function postHiboutik($rest_resource = "", $data = "")
  {
    try {
      if(empty($data)) {
        throw new \Exception("HiboutikAPI: post data is empty");
      }
      $this->post($this->account_connection."/".$rest_resource, json_encode($data));
      if ($this->error) {
        throw new \Exception("CURL error: ".$this->errorCode." : ".$this->errorMessage);
      } else {
        return $this->response;
      }
    } catch(\Exception $e) {
      trigger_error($e->getMessage()." -> ".$e->getTraceAsString(), E_USER_WARNING);
    }
  }




  public function putHiboutik($rest_resource = "", $data = "")
  {
    try {
      if(empty($data)) {
        throw new \Exception("HiboutikAPI: put data is empty");
      }
      $this->put($this->account_connection."/".$rest_resource, json_encode($data));
      if ($this->error) {
        throw new \Exception("CURL error: ".$this->errorCode." : ".$this->errorMessage);
      } else {
        return $this->response;
      }
    } catch(\Exception $e) {
      trigger_error($e->getMessage()." -> ".$e->getTraceAsString(), E_USER_WARNING);
    }
  }




  public function deleteHiboutik()
  {
    $args = func_get_args();
    $rest_resource = array_shift($args);
    $ids = implode('/', $args);
    try {
      if($rest_resource === NULL) {
        throw new \Exception("HiboutikAPI: deleteHiboutik() method -> No rest resource specified.");
      }
      if ($ids === '') {
        throw new \Exception("HiboutikAPI: deleteHiboutik() method needs an id to delete");
      }
      $this->delete($this->account_connection."/".$rest_resource."/".$ids);
      if ($this->error) {
        throw new \Exception("CURL error: ".$this->errorCode." : ".$this->errorMessage.' -> ');
      } else {
        return $this->response;
      }
    } catch(\Exception $e) {
      trigger_error($e->getMessage()." -> ".$e->getTraceAsString(), E_USER_WARNING);
    }
  }


}
