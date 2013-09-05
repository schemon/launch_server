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

$dbHandler = new DbHandler;
$appList = $dbHandler->fetchApplist('simon');
print json_encode(
  array(
    'ListResp' => array(
      'apps' => $appList
    )
  )
);
