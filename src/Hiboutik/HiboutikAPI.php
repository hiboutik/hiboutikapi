<?php

namespace Hiboutik;


/**
 *
 * @package Hiboutik\HiboutikAPI
 *
 * @version 2.5.0
 * @author  Hiboutik
 *
 * @license GPLv3
 * @license https://gnu.org/licenses/gpl.html
 *
 */
class HiboutikAPI implements HiboutikAPIInterface
{
  /** @var string API location */
  protected $uri;
  /** @var string Last HTTP request type */
  protected $request_type = '';
  /** @var string Last HTTP request resource */
  protected $request_resource = '';

  /** @var object Curl connection */
  public $hr;
  /** @var array Last HTTP request type */
  public $request_data = null;
  /** @var integer|null HTTP status code if different from 200 and 201 */
  public $errorCode = null;
  /** @var boolean True if HTTP status code is 200 or 201 */
  public $request_ok = false;
  /** @var boolean Set to false to get an object instead of an array */
  public $return_array = true;


/**
 * @param string      $account
 * @param string|null $user
 * @param string|null $pass
 * @param string      $api_version
 * @uses Hiboutik\HiboutikAPI\HttpRequest::basicAuth
 * @return array|null
 */
  public function __construct($account = '', $user = null, $pass = null, $api_version = '2')
  {
    switch ($api_version) {
      case '1':
        $this->uri = "https://$account.hiboutik.com/apirest";
       break;
      case '2':
        $this->uri = "https://$account.hiboutik.com/api";
        break;
       default:
        $this->uri = "https://$account.hiboutik.com/api";
    }

    $this->hr = new HiboutikAPI\HttpRequest('HiboutikAPI Client v2');

    if ($user !== null) {
      $this->hr->basicAuth($user, $pass);
    }
  }




/**
 * Legacy function
 *
 * @deprecated
 * @param string       $resource
 * @param array|object $data
 * @uses HiboutikAPI::_handleLegacyRequest
 * @return array|null
 */
  public function getHiboutik($resource = '', $data = null)
  {
    if (strpos($resource, '/') !== 0) {
      $resource = "/$resource";
    }
    return $this->_handleLegacyRequest($this->hr->get($this->uri.$resource, $data));
  }


/**
 * Legacy function
 *
 * @deprecated
 * @param string       $resource
 * @param array|object $data
 * @uses HiboutikAPI::_handleLegacyRequest
 * @return array|null
 */
  public function postHiboutik($resource = '', $data = null)
  {
    if (strpos($resource, '/') !== 0) {
      $resource = "/$resource";
    }
    return $this->_handleLegacyRequest($this->hr->post($this->uri.$resource, $data));
  }


/**
 * Legacy function
 *
 * @deprecated
 * @param string       $resource
 * @param array|object $data
 * @uses HiboutikAPI::_handleLegacyRequest
 * @return array|null
 */
  public function putHiboutik($resource = '', $data = null)
  {
    if (strpos($resource, '/') !== 0) {
      $resource = "/$resource";
    }
    return $this->_handleLegacyRequest($this->hr->put($this->uri.$resource, $data));
  }


/**
 * Legacy function
 *
 * @deprecated
 * @param string $resource
 * @uses HiboutikAPI::_handleLegacyRequest
 * @return array|null
 */
  public function deleteHiboutik($resource = '')
  {
    if (strpos($resource, '/') !== 0) {
      $resource = "/$resource";
    }
    return $this->_handleLegacyRequest($this->hr->delete($this->uri.$resource));
  }


/**
 * @internal
 *
 * @param string $result
 *
 * @return array|null
 */
  protected function _handleLegacyRequest($result)
  {
    $this->errorCode = null;
    $code = $this->hr->getCode();
    if ($code === 200 or $code === 201) {
      return json_decode($result);
    } else {
      $this->errorCode = $this->hr->getCode();
      return null;
    }
  }




/**
 * Sets OAuth authentication
 *
 * @param string $token
 * @uses Hiboutik\HiboutikAPI\HttpRequest::setOAuthToken
 * @return void
 */
  public function oauth($token)
  {
    $this->hr->setOAuthToken($token);
  }


/**
 * @param string     $resource API route
 * @param array|null $data
 * @uses HiboutikAPI::_handleRequest
 * @return array|string
 */
  public function get($resource = '', $data = null)
  {
    if (strpos($resource, '/') !== 0) {
      $resource = "/$resource";
    }
    $this->request_type = 'GET';
    $this->request_resource = $this->uri.$resource;
    $this->request_data = $data;
    return $this->_handleRequest($this->hr->get($this->uri.$resource, $data));
  }


/**
 * @param string            $resource API route
 * @param array|object|null $data
 * @param array  $files Files to upload in the following form:
 * [
 *   'image' => [
 *     [
 *       'file' => '/path/to/file',
 *       'type' => 'image/jpeg'
 *     ],
 *     [
 *       'file' => '/path/to/second/file',
 *       'type' => 'image/jpeg'
 *     ]
 *   ]
 * ]
 * The 'type' key is optional, defaults to 'application/octet-stream'.
 * @uses HiboutikAPI::_handleRequest
 * @return array|string
 */
  public function post($resource = '', $data = null, $files = null)
  {
    if (strpos($resource, '/') !== 0) {
      $resource = "/$resource";
    }
    $this->request_type = 'POST';
    $this->request_resource = $this->uri.$resource;
    $this->request_data = $data;
    if ($files === null) {
      return $this->_handleRequest($this->hr->post($this->uri.$resource, $data));
    } else {
      return $this->_handleRequest($this->hr->postFile($this->uri.$resource, $data, $files));
    }
  }


/**
 * @param string            $resource API route
 * @param array|object|null $data
 * @uses HiboutikAPI::_handleRequest
 * @return array|string
 */
  public function put($resource = '', $data = null)
  {
    if (strpos($resource, '/') !== 0) {
      $resource = "/$resource";
    }
    $this->request_type = 'PUT';
    $this->request_resource = $this->uri.$resource;
    $this->request_data = $data;
    return $this->_handleRequest($this->hr->put($this->uri.$resource, $data));
  }


/**
 * @param string $resource API route
 * @uses HiboutikAPI::_handleRequest
 * @return array|string
 */
  public function delete($resource = '')
  {
    if (strpos($resource, '/') !== 0) {
      $resource = "/$resource";
    }
    $this->request_type = 'DELETE';
    $this->request_resource = $this->uri.$resource;
    $this->request_data = null;
    return $this->_handleRequest($this->hr->delete($this->uri.$resource));
  }


/**
 * Repeat the last HTTP request
 *
 * Convenience method to repeat the last request made
 *
 * @uses HiboutikAPI::_handleRequest
 * @return array|string
 */
  public function repeat()
  {
    switch ($this->request_type) {
      case 'GET':
        return $this->_handleRequest($this->hr->get($this->request_resource, $this->request_data));
        break;
      case 'POST':
        return $this->_handleRequest($this->hr->post($this->request_resource, $this->request_data));
        break;
      case 'PUT':
        return $this->_handleRequest($this->hr->put($this->request_resource, $this->request_data));
        break;
      case 'DELETE':
        return $this->_handleRequest($this->hr->delete($this->request_resource));
        break;
      default:
        trigger_error('No request has been made to repeat', E_USER_ERROR);
    }
  }


/**
 * @internal
 *
 * @param string $result JSON data object
 * @return array|string
 */
  protected function _handleRequest($result)
  {
    $this->request_ok = false;
    if (empty($this->hr->error)) {
      $code = intval($this->hr->getCode());
      $response = json_decode($result, $this->return_array);
      if ($code < 400) {
        $this->request_ok = true;
      } else {
        if (!isset($response['error_description'])) {
          $response = [
            'error' => 'unknown_error',
            'code' => 99,
            'error_description' => 'Unknown error',
            'details' => [
              'http_code' => $code,
              'response' => $response
            ]
          ];
        }
      }
    } else {
      $response = [
        'error' => 'curl_error',
        'code' => $this->hr->error['error']['code'],
        'error_description' => $this->hr->error['error']['error_description'],
        'details' => []
      ];
    }
    return $response;
  }


/**
 * Returns an array with HTTP request's complete status informations
 *
 * @return array
 */
  public function getStatus()
  {
    return $this->hr->status();
  }


/**
 * Get an HTTP field or all of them
 *
 * Returns an array with HTTP request's headers if called without argument.
 * Supply an argument - an HTTP header field - to get its value. NULL is
 * returned if the field is invalid.
 *
 * @param string|null $field Http field name
 * @return array|string|null
 */
  public function headers($field = null)
  {
    return $this->hr->getHeader($field);
  }


/**
 * Returns an array with paging informations
 *
 * @return array
 * <code>
 * [
 *   'start'   => [
 *     'number' => 1,
 *     'link'   => 'https://my_account.hiboutik.com/api/customers/?p=1',
 *   ],
 *   'prev'    => [
 *     'number' => 9,
 *     'link'   => 'https://my_account.hiboutik.com/api/customers/?p=9',
 *   ],
 *   'current' => [
 *     'number' => 10,
 *     'link'   => 'https://my_account.hiboutik.com/api/customers/?p=10',
 *   ]',
 *   'next'    => [
 *     'number' => 11,
 *     'link'   => 'https://my_account.hiboutik.com/api/customers/?p=11',
 *   ],
 *   'last'    => [
 *     'number' => 28,
 *     'link'   => 'https://my_account.hiboutik.com/api/customers/?p=28',
 *   ],
 * ]
 * </code>
 */
  public function pagination()
  {
    $link_header = $this->hr->getHeader('Link');
    if ($link_header === null) {
      return [];
    }
    $links = explode(',', $link_header);
    $pagination = [];
    foreach ($links as $page) {
      preg_match('/p=(\d+)/', $page, $p);
      preg_match('/rel="(\w+)"/', $page, $rel);
      $pagination[$rel[1]] = [
        'number' => $p[1],
        'link' => substr(trim(explode(';', $page)[0]), 1, -1)
      ];
    }
    return $pagination;
  }
}
