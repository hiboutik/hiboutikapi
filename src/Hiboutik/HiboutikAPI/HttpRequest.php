<?php

namespace Hiboutik\HiboutikAPI;


/**
 * @package Hiboutik\HiboutikAPI\HttpRequest
 *
 * @version 1.3.0
 * @author  Hiboutik
 *
 * @license GPLv3
 * @license https://gnu.org/licenses/gpl.html
 *
 */
class HttpRequest implements HttpRequestInterface
{
  /** @var array Headers of the last request made */
  public $current_headers = [];
  /** @var array Headers to be sent with the next request. Shape: $key => $value */
  public $send_headers = [];
  /** @var object Curl connection */
  public $curl;
  /** @var array Default curl options */
  public $curl_opts_default = [
    CURLOPT_AUTOREFERER    => false,
    CURLOPT_FORBID_REUSE   => false,
    CURLOPT_FRESH_CONNECT  => false,
    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
  ];
  /** @var array Curl options */
  public $curl_opts = [];
  /** @var array Curl error */
  public $error = [];


/**
 * Default constructor
 *
 * @param string $ua User agent
 * @param array $curl_opts Curl options to override or to set
 */
  public function __construct($ua = '-', $opts = null)
  {
    if (!extension_loaded('curl')) {
      throw new \ErrorException('cURL library is not loaded');
    }
    $this->curl = curl_init();
    $this->curl_opts_default[CURLOPT_USERAGENT] = $ua;
    $this->curl_opts_default[CURLOPT_HEADERFUNCTION] = $this->_makeCallback();
    if (is_array($opts)) {
      $this->curl_opts_default = array_merge($this->curl_opts_default, $opts);
    }
    curl_setopt_array($this->curl, $this->curl_opts_default);
  }


/**
 * Default destructor
 *
 * @return integer
 */
  public function __destruct()
  {
    curl_close($this->curl);
  }


/**
 * Reset curl
 *
 * @return object HttpRequest ($this)
 */
  public function resetCurl()
  {
    if (function_exists('curl_reset')) {
      curl_reset($this->curl);
    } else {
      curl_close($this->curl);
      $this->curl = curl_init();
    }
    curl_setopt_array($this->curl, $this->curl_opts_default);
    return $this;
  }


/**
 * Make a get request
 *
 * @param string $url
 * @param array|null $data
 * @return string
 */
  public function get($url, $data = null)
  {
    /**
     * PHP versions inferior to 5.5.11 have a bug what does not reset the
     * 'CURLOPT_CUSTOMREQUEST' when setting it to null, but to an empty string.
     * Bad request error ensues.
     */

    if ((version_compare(PHP_VERSION, '5.5.11', '<') or defined('HHVM_VERSION'))) {
      if (isset($this->curl_opts[CURLOPT_CUSTOMREQUEST])) {
        $this->curl_opts[CURLOPT_CUSTOMREQUEST] = 'GET';
      }
    }
    $this->curl_opts[CURLOPT_URL] = $data === null ? $url : $url.'?'.http_build_query($data, '', '&');
    $this->curl_opts[CURLOPT_HTTPGET] = true;
    if (!empty($this->send_headers)) {
      $this->curl_opts[CURLOPT_HTTPHEADER] = $this->_prepareHeaders();
    }

    $this->_setOptions();

    return $this->_exec();
  }


/**
 * Make a post request
 *
 * This method does not support file uploads.
 * 'Content-Type' is either 'application/json' or
 * 'application/x-www-form-urlencoded'
 *
 * @param string $url
 * @param array|object|null $data
 * @param boolean $is_json
 * @return string
 */
  public function post($url, $data = null, $is_json = false)
  {
    /**
     * If $data is "url encoded" the content type is "application/x-www-form-
     * urlencoded", otherwise, if the data passed is an array the content type
     * will be "multipart/form-data;boundary=------------------------a83e...."
    */
    if ($is_json) {
      $this->setHeaders('Content-Type', 'application/json');
      $send_data = json_encode($data);
    } else {
      if (is_array($data) or is_object($data)) {
        $send_data = http_build_query($data, '', '&');
      } else {
        $send_data = http_build_query([$data], '', '&');
      }
    }

    /**
     * PHP versions inferior to 5.5.11 have a bug what does not reset the
     * 'CURLOPT_CUSTOMREQUEST' when setting it to null, but to an empty string.
     * Bad request error ensues.
     */
    if ((version_compare(PHP_VERSION, '5.5.11', '<') or defined('HHVM_VERSION'))) {
      if (isset($this->curl_opts[CURLOPT_CUSTOMREQUEST])) {
        $this->curl_opts[CURLOPT_CUSTOMREQUEST] = 'POST';
      }
    }
    $this->curl_opts[CURLOPT_URL] = $url;
    $this->curl_opts[CURLOPT_POST] = true;
    $this->curl_opts[CURLOPT_POSTFIELDS] = $send_data;
    if (!empty($this->send_headers)) {
      $this->curl_opts[CURLOPT_HTTPHEADER] = $this->_prepareHeaders();
    }

    $this->_setOptions();

    return $this->_exec();
  }


/**
 * Make a put request
 *
 * This method does not support file uploads.
 * 'Content-Type' is either 'application/json' or
 * 'application/x-www-form-urlencoded'
 *
 * @param string $url
 * @param array|object|null $data
 * @param boolean $is_json
 * @return string
 */
  public function put($url, $data = null, $is_json = false)
  {
    if ($is_json) {
      $this->setHeaders('Content-Type', 'application/json');
      $send_data = json_encode($data);
    } else {
      if (is_array($data) or is_object($data)) {
        $send_data = http_build_query($data, '', '&');
      } else {
        $send_data = http_build_query([$data], '', '&');
      }
    }
    $this->curl_opts[CURLOPT_URL] = $url;
    $this->curl_opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
    $this->curl_opts[CURLOPT_POSTFIELDS] = $send_data;
    if (!empty($this->send_headers)) {
      $this->curl_opts[CURLOPT_HTTPHEADER] = $this->_prepareHeaders();
    }

    $this->_setOptions();
    $this->curl_opts[CURLOPT_CUSTOMREQUEST] = null;

    return $this->_exec();
  }


/**
 * Make a delete request
 *
 * @param string $url
 * @return string
 */
  public function delete($url)
  {
    $this->curl_opts[CURLOPT_URL] = $url;
    $this->curl_opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
    if (!empty($this->send_headers)) {
      $this->curl_opts[CURLOPT_HTTPHEADER] = $this->_prepareHeaders();
    }

    $this->_setOptions();
    $this->curl_opts[CURLOPT_CUSTOMREQUEST] = null;

    return $this->_exec();
  }


/**
 * Upload a file
 *
 * @param string $url
 * @param array  $data Post data in key => value form
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
 * The 'type' key is optional. If not set, 'type' will be 'application/octet-stream'.
 * @return string
 */
  public function postFile($url, $data, $files)
  {
    if ((version_compare(PHP_VERSION, '5.5.11', '<') or defined('HHVM_VERSION'))) {
      if (isset($this->curl_opts[CURLOPT_CUSTOMREQUEST])) {
        $this->curl_opts[CURLOPT_CUSTOMREQUEST] = 'POST';
      }
    }

    $files_array = [];
    foreach ($files as $name => $input) {
      if (count($input) > 1) {
        foreach ($input as $key => $file) {
          if (!file_exists($file['file'])) {
            $this->error = $this->_handleError('file_not_found', 'The file to upload was not found');
            return '';
          }
          $files_array["{$name}[$key]"] = $this->_createCurlFile($file);
        }
      } else {
        if (!file_exists($input[0]['file'])) {
          $this->error = $this->_handleError('file_not_found', 'The file to upload was not found');
          return '';
        }
        $files_array["{$name}"] = $this->_createCurlFile($input[0]);
      }
    }

    if (version_compare(PHP_VERSION, '5.5.0', '>') and version_compare(PHP_VERSION, '5.6.0', '<')) {
      $this->curl_opts[CURLOPT_SAFE_UPLOAD] = true;
    }

    $this->curl_opts[CURLOPT_URL] = $url;
    $this->curl_opts[CURLOPT_POST] = true;
    if (is_array($data)) {
      $send_data = array_merge($data, $files_array);
    } else {
      $send_data = $files_array;
    }
    $this->curl_opts[CURLOPT_POSTFIELDS] = $send_data;

    $this->_setOptions();

    return $this->_exec();
  }


/**
 * @internal
 *
 * Returns a file to upload
 *
 * @param array $file Format:
 * [
 *   'file' => '/path/to/file',
 *   'type' => 'image/jpeg'
 * ]
 * The 'type' key is optional.
 *
 * @return string|CURLFile
 */
  protected function _createCurlFile($file)
  {
    // PHP < 5.5.0 does not have the 'CURLFile' class, the '@' tag is used to prefix file paths
    if ((version_compare(PHP_VERSION, '5.5.0', '<'))) {
      return "@{$file['file']}".(isset($file['type']) ? ";type={$file['type']}" : '');
    } else {
      $cfile = new \CURLFile($file['file']);
      if (isset($file['type'])) {
        $cfile->setMimeType($file['type']);
      }
      $cfile->setPostFilename(basename($file['file']));
      return $cfile;
    }
  }


/**
 * Execute a curl request
 *
 * @return string
 */
  protected function _exec()
  {
    $result = curl_exec($this->curl);
    if ($result === false) {
      $this->error = $this->_handleError(curl_errno($this->curl), curl_error($this->curl));
      return '';
    } else {
      $this->error = [];
      return $result;
    }
  }


/**
 * Get request status
 *
 * @return array
 */
  public function status()
  {
    $return_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    if ($return_code == 0) {
      return $this->_handleError(curl_errno($this->curl), curl_error($this->curl));
    } else {
      return curl_getinfo($this->curl);
    }
  }


/**
 * @internal
 *
 * Check curl errors
 *
 * @return array
 */
  protected function _handleError($code, $error_description)
  {
    return [
      'error' => [
        'code' => $code,
        'error_description' => $error_description
      ]
    ];
  }


/**
 * Set user agent
 *
 * @param string $ua User agent name
 * @return object HttpRequest ($this)
 */
  public function setUserAgent($ua)
  {
    curl_setopt($this->curl, CURLOPT_USERAGENT, $ua);
    return $this;
  }


/**
 * Get all or a header field
 *
 * @param string $field Optional
 * @return array|string
 */
  public function getHeader($field = null)
  {
    if ($field !== null) {
      if (isset($this->current_headers[$field])) {
        return $this->current_headers[$field];
      } else {
        return null;
      }
    } else {
      return $this->current_headers;
    }
  }


/**
 * Set headrs to send
 *
 * @param string $header_name
 * @param string $header_value
 * @return object HttpRequest ($this)
 */
  public function setHeaders($header_name = '', $header_value = '')
  {
    $this->send_headers[$header_name] = $header_value;
    return $this;
  }


/**
 * @internal
 *
 * Convert headers array to right format
 *
 * The headers are stored in an array shaped as $key => $value pairs. This
 * enables replacement.
 * The Curl class expects the shape of the array to be:
 * <code>
 *   [
 *     'Content-type: application/json',
 *     'Cache-Control: no-cache, must-revalidate'
 *   ];
 * </code>
 * Hence this method.
 *
 * @return array
 */
  protected function _prepareHeaders()
  {
    $headers = [];
    foreach ($this->send_headers as $key => $value) {
      $headers[] = "$key: $value";
    }
    return $headers;
  }


/**
 * Set basic authentication
 *
 * @param string $user
 * @param string $password
 * @return object HttpRequest ($this)
 */
  public function basicAuth($user, $password)
  {
    $this->curl_opts[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
    $this->curl_opts[CURLOPT_USERPWD] = "$user:$password";
    return $this;
  }


/**
 * Unset basic authentication
 *
 * @return object HttpRequest ($this)
 */
  public function stopBasicAuth()
  {
    curl_setopt_array($this->curl, [
      CURLOPT_HTTPAUTH => null,
      CURLOPT_USERPWD => null
    ]);
    return $this;
  }


/**
 * Set OAuth token
 *
 * @param string $token
 * @return object HttpRequest ($this)
 */
  public function setOAuthToken($token)
  {
  /**
   * Note: CURLOPT_XOAUTH2_BEARER doesn't work for now
   */
    $this->setHeaders('Authorization', "Bearer $token");
    return $this;
  }


/**
 * Get last HTTP code
 *
 * @return integer
 */
  public function getCode()
  {
    return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
  }


/**
 * @internal
 *
 * Get request's headers
 *
 * This method is called after the request is complete and builds an array with
 * headers fields.
 *
 * @return function
 */
  protected function _makeCallback()
  {
    return function ($ch, $header_data)
    {
      if (stripos($header_data, 'HTTP/') === false and $header_data !== "\r\n") {
        preg_match('/([A-z0-9-]+):\s*(.*)$/', $header_data, $matches);
        $this->current_headers[$matches[1]] = $matches[2];
      }
      return strlen($header_data);
    };
  }


/**
 * Send result to file
 *
 * @param strinf $destination Path\to\file
 * @return object HttpRequest ($this)
 */
  public function toFile($destination)
  {
    $file = fopen($destination, 'w');
    curl_setopt($this->curl, CURLOPT_FILE, $file);
    return $this;
  }


/**
 * Set a curl option
 *
 * @return integer
 */
  public function setOpt($key, $value)
  {
    curl_setopt($this->curl, $key, $value);
  }


/**
 * @internal
 *
 * Sets headers in the Curl object
 *
 * @return void
 */
  protected function _setOptions()
  {
    if (!curl_setopt_array($this->curl, $this->curl_opts)) {
      throw new \Exception('Class Hiboutik\HttpRequest: invalid option;');
    }
    $this->curl_opts = [];
  }
}
