<?php
    /*** nullify any existing autoloads ***/
    spl_autoload_register(null, false);

    /*** specify extensions that may be loaded ***/
    spl_autoload_extensions('.php, .class.php');

   
    /*** class Loader ***/
    function classLoader($class)
    {
        $filename = $class . '.class.php';
        $file ='' . $filename;
        if (!file_exists($file))
        {
            return false;
        }
        include $file;
    }
  /*** register the loader functions ***/
  spl_autoload_register('classLoader');
  
  error_log(var_export($_REQUEST['req'], TRUE));

  if(!isset($_REQUEST['req'])) {
    error_log(var_export($_REQUEST, TRUE));
    $response = 'Error missing request params';
  } else {
    $req = json_decode($_REQUEST['req']);
    $rh = new RequestHandler();
    $response = $rh->handle($req);
  }
  print $response;


