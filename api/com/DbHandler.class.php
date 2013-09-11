<?php
  class DbHandler {

    private $dbh;

     public function __construct() {
       $user = 'root';
       $pass = 'FrukE2pE';
       $this->dbh = new PDO('mysql:host=launcherdb.cpu4kl1cdbe3.eu-west-1.rds.amazonaws.com;port=3306;dbname=launcher', $user, $pass);
     } 

     public function fetchAppList($owner = NULL) {
       $result = array();
       $attr0 = $this->dbh->quote($owner);
       $queryString = 'select * from app_list where owner like ' . $attr0 . ';';
       $queryResult = $this->performQuery($queryString);
       $apps = $queryResult->fetchAll(PDO::FETCH_OBJ);
       foreach($apps as $app) {
         $appTile = new StdClass();
         $appTile->packageName = $app->package_name;
         $appTile->position = $app->position;
         $appTile->title = $app->title;
         $appTile->iconUri = $app->icon_url;
         $item = new StdClass();
         $item->appTile = $appTile;
         $result[] = $item;
       }
       return $result; 
     }

    public function saveDeviceInfo($info) {
      $device_token = $this->dbh->quote($info->deviceToken);
      $launcher_id = $this->dbh->quote($info->launcherId);
      $android_id = $this->dbh->quote($info->androidId);
      $gcm_registration_id = $this->dbh->quote($info->gcmRegistrationId);
      
      $queryString = 
        'INSERT INTO device 
         (device_token, launcher_id, android_id, gcm_registration_id)
         values('
         . $device_token
         . ',' . $launcher_id
         . ',' . $android_id
         . ',' . $gcm_registration_id
         . ')'
         . ' ON DUPLICATE KEY UPDATE '
         . 'launcher_id' . '=' . $launcher_id
         . ',' . 'android_id' . '=' . $android_id
         . ',' . 'gcm_registration_id' . '=' . $gcm_registration_id
         . ';';

      $queryResult = $this->performQuery($queryString);
      $numOfChangedRows = $queryResult->rowCount();
      $result = 0;
      if(1 === $numOfChangedRows) {
        $result = 1;
      }
      return $result; 
   }

   public function getGcmRegistrationIds($launcherId) {
     $launcher_id = $this->dbh->quote($launcherId);
     $queryString = 
       'SELECT DISTINCT gcm_registration_id FROM device 
        WHERE launcher_id = ' . $launcher_id . ';';

     $result = $this->performQuery($queryString)->fetchAll(PDO::FETCH_COLUMN);
     error_log(var_export($result, TRUE));
     return $result;
   }

     private function performQuery($queryString) {
       return $this->dbh->query($queryString);
     }

  }
