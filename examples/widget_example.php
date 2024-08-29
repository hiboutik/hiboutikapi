<?php
namespace Hiboutik;


class Widget
{
  const E_NONE = 0;
  const E_HEADER_TOKEN = 1;
  const E_HEADER_TOKEN_TIME = 2;
  const E_TOKEN_EXPIRED = 3;
  const E_BAD_TOKEN = 4;

  public $account;
  public $secret;
  public $time_limit;
  public $head = [];
  public $body = '';


/**
 * @arg $account     Hiboutik account; Eg: for https://aaaa.hiboutik.com the account is 'aaaa'
 * @arg $secret      App secret found in your Hiboutik account
 * @arg $time_limit  Time limit for the token validity
 */
  public function __construct($account, $secret, $time_limit = 5)
  {
    $this->account = $account;
    $this->secret = $secret;
    $this->time_limit = $time_limit;
  }


/**
 * @arg $data  Head data
 */
  public function addHead($data)
  {
    $this->head = $data;
  }


/**
 * @arg $html  HTML content to display in Hiboutik
 */
  public function addBody($html)
  {
    $this->body = $html;
  }


/**
 * Check if the request is legit
 */
  protected function _checkTokens()
  {
    if (!isset($_SERVER['HTTP_X_HIBOUTIK_TOKEN'])) {
      return [
        'error'      => 'header_token_missing',
        'error_code' => self::E_HEADER_TOKEN
      ];
    } else {
      $header_token = $_SERVER['HTTP_X_HIBOUTIK_TOKEN'];
    }
    if (!isset($_SERVER['HTTP_X_HIBOUTIK_TOKEN_TIME'])) {
      return [
        'error'      => 'header_time_missing',
        'error_code' => self::E_HEADER_TOKEN_TIME
      ];
    } else {
      $header_time = $_SERVER['HTTP_X_HIBOUTIK_TOKEN_TIME'];
    }
    $now = time();
    if ($now - $header_time > $this->time_limit) {
      return [
        'error'      => 'token_expired',
        'error_code' => self::E_TOKEN_EXPIRED
      ];
    }

    $my_token = hash_hmac('sha256', $header_time, $this->secret);

    if (!hash_equals($my_token, $header_token)) {
      return [
        'error'      => 'bad_token',
        'error_code' => self::E_BAD_TOKEN
      ];
    }

    return [
      'error'      => '',
      'error_code' => self::E_NONE
    ];
  }


/**
 * Put everything toghether
 */
  public function run()
  {
    header('Access-Control-Allow-Method: OPTIONS,GET');
    header('Access-Control-Allow-Origin: https://'.$this->account.'.hiboutik.com');
    header('Access-Control-Allow-Headers: x-hiboutik-token, x-hiboutik-token-time');
    header('Content-type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      return [
        'error'      => '',
        'error_code' => self::E_NONE
      ];
    }

    $result = $this->_checkTokens();
    if ($result['error_code'] !== self::E_NONE) {
      header('HTTP/1.1 403 Forbidden');
      $this->head = [];
      $this->body = 'WIDGET ERROR';
    }
    print json_encode([
      'head' => $this->head,
      'body' => $this->body
    ]);

    return $result;
  }
}




$account = 'your_account';
$secret = 'your_secret';


$id_client = $_GET['id_client'];
$sale_id = $_GET['sale_id'];
$customer_id = $_GET['customer_id'];

$w = new Widget($account, $secret, 10);
$html = <<<HTML
<p>
  Client: $id_client
</p>
<h4>Mon formulaire distant</h4>
<form action="#" target="_blank">
  <input type="text" name="text" value="">
  <button type="submit" class="btn btn-info">Valider</button>
</form>
HTML;


$w->addBody($html);
$result = $w->run();


if ($result['error_code'] !== 0) {
  trigger_error(json_encode($result));
}

