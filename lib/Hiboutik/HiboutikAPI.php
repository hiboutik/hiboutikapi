<?php
/**
 *
 *  Class HiboutikAPI
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   This class makes API calls for the Hiboutik API. For a complete documentations refer to your Hiboutik account:
 *   SETTINGS -> USERS -> Click on the wrench symbol.
 *
 *
 *
 *  @INSTALL:
 *  ---------
 *
 *  With Composer:
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
 *
 *
 *
 *  Manual install:
 *
 *  Download the Curl class: https://github.com/php-curl-class/php-curl-class/tree/master/src/Curl with its three files.
 *  Include them in your file and then the HiboutikAPI class.
 *
 *  <code>
 *    include "CaseInsensitiveArray.php";
 *    include "Curl.php";
 *    include "MultiCurl.php";
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
 *    $user = "user@email.com";//   Hiboutik user name: the email address you use to connect to your account.
 *    $key = "ORECJF05UQZ133Z1O4O0Z2XLZ7TCSAVZP01Z";//  Hiboutik API token
 *
 *    $hiboutik = new \Hiboutik\HiboutikAPI($account, $user, $key);//   Initialize
 *
 *    //  Get all products
 *    $products = $hiboutik->getHiboutik("products");
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
 *    This method returns an array with the requested data. For a complete reference of the Hiboutik API see the
 *    documentation via your Hiboutik account.
 *    @param
 *      $rest_resource        : string, required
 *    @return
 *      array
 *
 *  $hiboutik->postHiboutik($rest_resource, $data);
 *             -----------------------------------
 *    This method sends post requests. The $data array must be formatted according to the Hiboutik API.
 *    Returns the last inserted id.
 *    @param
 *      $rest_resource        : string, required
 *      $data                 : array, required
 *    @return
 *      array
 *
 *  $hiboutik->putHiboutik($rest_resource, $data);
 *             ----------------------------------
 *    This method sends put requests. The $data array must be formatted according to the Hiboutik API.
 *    @param
 *      $rest_resource        : string, required
 *      $data                 : array, required
 *    @return
 *      array
 *
 *
 *  $hiboutik->deleteHiboutik($rest_resource, $id);
 *             -----------------------------------
 *    This method sends delete requests. The $data array must be formatted according to the Hiboutik API.
 *    @param
 *      $rest_resource        : string, required
 *      $id                   : string, required
 *    @return
 *      array
 *
 *
 *  $hiboutik->debug($debug);
 *             -------------
 *    This method activates error display.
 *    It is set to "false" by default - no  error messages are sent to log.
 *    @param
 *      $debug                : $bool
 *    @return
 *      $this
 *
 */




namespace Hiboutik;
use Curl;


class HiboutikAPI extends Curl\Curl
{

  const VERSION_API = "1.0.1";

  protected $account_connection;
  protected $user_connection;
  protected $key_connection;
  protected $debug;



  public function __construct($account, $user, $key)
  {
    try {
      if(count(func_get_args()) !== 3) {
        throw new \Exception("HiboutikAPI: The contructor expects exactly 3 (three) arguments.");
      }

      $this->account_connection   = "https://".$account.".hiboutik.com/apirest";
      $this->user_connection      = $user;
      $this->key_connection       = $key;
      $this->debug                = false;

      parent::__construct();

      $user_agent     = "HiboutikAPI v".self::VERSION_API." (+https://github.com/hiboutik/hiboutikapi)";
      $user_agent    .= " PHP/" . PHP_VERSION;
      $curl_version   = curl_version();
      $user_agent    .= ' curl/' . $curl_version['version'];
      $this->setUserAgent($user_agent);

      $this->setBasicAuthentication($user, $key);
    } catch(\Exception $e) {
      trigger_error($e->getMessage(), E_USER_ERROR);
    }
  }




  public function debug($debug = false)
  {
    if($debug === true) {
      $this->debug = true;
    }

    return $this;
  }




  public function getHiboutik($rest_resource = "")
  {
    try {

      $this->setHeader('Content-Type', 'application/json');
      $this->get($this->account_connection."/".$rest_resource."");

      if ($this->error) {
        throw new \Exception("CURL error: ".$this->errorCode." : ".$this->errorMessage);
      } else {
        return $this->response;
      }
    } catch(\Exception $e) {
      if($this->debug === true) {
        trigger_error($e->getMessage(), E_USER_ERROR);
      }
    }
  }




  public function postHiboutik($rest_resource = "", $data = "")
  {
    try {
      if(empty($data)) {
        throw new \Exception("HiboutikAPI: post data is empty");
      }

      $this->setHeader('Content-Type', 'application/json');
      $this->post($this->account_connection."/".$rest_resource."", json_encode($data));

      if ($this->error) {
        throw new \Exception("CURL error: ".$this->errorCode." : ".$this->errorMessage);
      } else {
        return $this->response;
      }
    } catch(\Exception $e) {
      if($this->debug === true) {
        trigger_error($e->getMessage(), E_USER_ERROR);
      }
    }
  }




  public function putHiboutik($rest_resource = "", $data = "")
  {
    try {
      if(empty($data)) {
        throw new \Exception("HiboutikAPI: put data is empty");
      }

      $this->setHeader('Content-Type', 'application/json');
      $this->put($this->account_connection."/".$rest_resource."", json_encode($data));

      if ($this->error) {
        throw new \Exception("CURL error: ".$this->errorCode." : ".$this->errorMessage);
      } else {
        return $this->response;
      }
    } catch(\Exception $e) {
      if($this->debug === true) {
        trigger_error($e->getMessage(), E_USER_ERROR);
      }
    }
  }




  public function deleteHiboutik($rest_resource = "", $id)
  {
    try {
      if(empty($id)) {
        throw new \Exception("HiboutikAPI: deleteHiboutik() method needs an id to delete");
      }

      if($rest_resource === "") {
        throw new \Exception("HiboutikAPI: deleteHiboutik() method -> No resource specified.");
      }

      $this->setHeader('Content-Type', 'application/json');
      $this->delete($this->account_connection."/".$rest_resource."/".$id);

      if ($this->error) {
        throw new \Exception("CURL error: ".$this->errorCode." : ".$this->errorMessage);
      } else {
        return $this->response;
      }
    } catch(\Exception $e) {
      if($this->debug === true) {
        trigger_error($e->getMessage(), E_USER_ERROR);
      }
    }
  }

}

?>
