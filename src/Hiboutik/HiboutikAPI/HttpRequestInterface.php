<?php

namespace Hiboutik\HiboutikAPI;

interface HttpRequestInterface
{
/**
 * Make a get request
 *
 * @param string $url
 * @param array|null $data
 * @return string
 */
  public function get($resource, $data = null);


/**
 * Make a post request
 *
 * @param string $url
 * @param array|object|null $data
 * @param boolean $is_json
 * @return string
 */
  public function post($resource, $data = null, $is_json = false);


/**
 * Make a put request
 *
 * @param string $url
 * @param array|object|null $data
 * @param boolean $is_json
 * @return string
 */
  public function put($resource, $data = null);


/**
 * Make a delete request
 *
 * @param string $url
 * @return string
 */
  public function delete($resource);


/**
 * Get request status
 *
 * @return array
 */
  public function status();


/**
 * Set headrs to send
 *
 * @param string $header_name
 * @param string $header_value
 * @return object HttpRequest ($this)
 */
  public function setHeaders($header_name, $header_value);


/**
 * Set basic authentication
 *
 * @param string $user
 * @param string $password
 * @return object HttpRequest ($this)
 */
  public function basicAuth($user, $password);


/**
 * Set OAuth token
 *
 * @param string $token
 * @return object HttpRequest ($this)
 */
  public function setOAuthToken($token);
}
