<?php
/**
 * Bootstrapping functions, essential and needed for Mithridates to work together with some common helpers. 
 *
 */
 
/**
 * Default exception handler.
 *
 */
function myExceptionHandler($exception) {
  echo "Mithridates: Uncaught exception: <p>" . $exception->getMessage() . "</p><pre>" . $exception->getTraceAsString(), "</pre>";
}
set_exception_handler('myExceptionHandler');
 
 
/**
 * Autoloader for classes.
 *
 */
 
function myAutoloader($class) {
    
    $isFileFound = false;
    $dir = MITHRIDATES_INSTALL_PATH . "/src/*";
    $dirs = array_filter(glob($dir), 'is_dir');
    foreach ($dirs as $dir) {
        $path = $dir . "/{$class}.php";
        if(is_file($path)) {
          include($path);
          $isFileFound = true;
        }
    }
    if (!$isFileFound) {
        throw new Exception("Classfile '{$class}' does not exists.");
    }

    
    /*
  $path = MITHRIDATES_INSTALL_PATH . "/src/{$class}/{$class}.php";
  if(is_file($path)) {
    include($path);
  }
  else {
    throw new Exception("Classfile '{$class}' does not exists.");
  }*/
  
}

spl_autoload_register('myAutoloader');

/**
* Dump function.
*
*/
function dump($array) {
  echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}
