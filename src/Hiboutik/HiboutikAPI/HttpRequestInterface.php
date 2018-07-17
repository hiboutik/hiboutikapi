<?php

namespace Hiboutik\HiboutikAPI;

interface HttpRequestInterface
{
  public function get($resource, $data = null);
  public function post($resource, $data = null, $is_json = false);
  public function put($resource, $data = null);
  public function delete($resource);
  public function status();
  public function setHeaders($header_name, $header_value);
  public function basicAuth($user, $password);
}
