<?php

namespace Hiboutik\HiboutikAPI;


function autoload($class_name) {
  $path =  __DIR__.'/../../'.str_replace('\\','/', $class_name).'.php';
  if(is_readable($path)) {
    require $path;
  }
}

spl_autoload_register("Hiboutik\HiboutikAPI\autoload");
