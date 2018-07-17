<?php

namespace Hiboutik;


interface HiboutikAPIInterface
{
/**
 * @param string $resource
 * rest resource ("/products"; "/customers/568/products_solds")
 * @param array|object $data The array must have keys that are legal php and
 * CGI variable names
 */
  public function getHiboutik($resource = "", $data = null);


/**
 * @param string $resource
 * rest resource ("/products"; "/customers/568/products_solds")
 * @param array|object $data The array must have keys that are legal php and
 * CGI variable names
 */
  public function postHiboutik($resource = "", $data = null);


/**
 * @param string $resource
 * rest resource ("/products"; "/customers/568/products_solds")
 * @param array|object $data The array must have keys that are legal php and
 * CGI variable names
 */
  public function putHiboutik($resource = "", $data = null);


/**
 * @param string $resource
 * rest resource ("/products"; "/customers/568/products_solds")
 */
  public function deleteHiboutik($resource = "");
}
