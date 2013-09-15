<?php
  class DbHandler {

    private $dbh;

     public function __construct() {
       $user = 'root';
       $pass = 'FrukE2pE';
       $this->dbh = new PDO('mysql:host=launcherdb.cpu4kl1cdbe3.eu-west-1.rds.amazonaws.com;port=3306;dbname=launcher', $user, $pass);
     }


     public function getAppListAll($launcher_id) {
       $launcher_id = $this->dbh->quote($launcher_id);
       $query_string = '
         SELECT id FROM app_list '
         . 'WHERE launcher_id LIKE ' . $launcher_id . ';';
       $queryResult = $this->performQuery($query_string);
       $lists = $queryResult->fetchAll(PDO::FETCH_OBJ);
       return $lists;
     }

     public function createNewAppList($launcher_id, $platform) {
       $launcher_id = $this->dbh->quote($launcher_id);
       $platform = $this->dbh->quote($platform);
       $query_string = '
         INSERT INTO app_list (launcher_id, platform)'
         . 'VALUES ('
         . $launcher_id
         . ',' . $platform
         . ');';
       $result = $this->insert($query_string);
       return $result;
     }

     public function deleteList($app_list_id) {
       $app_list_id = $this->dbh->quote($app_list_id);
       $query_string = '
         DELETE FROM app_list WHERE id = ' . $app_list_id . ';';
       $result = $this->insert($query_string);
       return $result;
     }

     public function fetchAppList($id = NULL) {
       $result = array();
       $attr0 = $this->dbh->quote($id);
       $queryString = '
        SELECT * FROM 
        app_in_list LEFT JOIN app ON app_in_list.app_id = app.id
        WHERE 
        app_list_id = ' . $attr0 .
        ';';
       error_log($queryString);
       $queryResult = $this->performQuery($queryString);
       $apps = $queryResult->fetchAll(PDO::FETCH_OBJ);
 
       error_log(var_export($apps, TRUE));     
  
       foreach($apps as $app) {
         $appTile = new StdClass();
         $appTile->packageName = $app->app_id;
         $appTile->position = $app->position;
         $appTile->title = $app->title;
         if($app->developer) {
           $appTile->developer = $app->developer;
         }
         $appTile->iconUri = $app->icon_url;
         if($app->latitude && $app->longitude) {
           $appTile->latitude = $app->latitude;
           $appTile->longitude = $app->longitude;
         }
         $item = new StdClass();
         $item->appTile = $appTile;
         $result[] = $item;
       }
       return $result; 
     }

    public function addToAppList($app_id, $app_list_id, $position = NULL, $latitude = NULL, $longitude = NULL) {
      $app_id = $this->dbh->quote($app_id);
      $app_list_id = $this->dbh->quote($app_list_id);

     $is_updating = FALSE;

      if(NULL != $position) {
        $column_position = 'position';
        $value_position = $this->dbh->quote($position);
        $update_position = 'position=' . $value_position;
      } else {
        $column_position = 'position';
        $value_position = '\'-1\'';
        $update_position = 'position=-1';
      }

      if(NULL != $latitude) {
        $column_latitude = 'latitude';
        $value_latitude = $this->dbh->quote($latitude);
        $update_latitude = 'latitude=' . $value_latitude;
      } else {
        $column_latitude = 'latitude';
        $value_latitude = '\'0\'';
        $update_latitude = 'latitude=\'0\'';
      }
     
      if(NULL != $longitude) {
        $column_longitude = 'longitude';
        $value_longitude = $this->dbh->quote($longitude);
        $update_longitude = 'longitude=' . $value_longitude;
      } else {
        $column_longitude = 'longitude';
        $value_longitude = '\'0\'';
        $update_longitude = 'longitude=\'0\'';
      }

      $update_string = ' ON DUPLICATE KEY UPDATE '
        . $update_position
        . ',' . $update_latitude
        . ',' . $update_longitude;

      $query_string = '
        INSERT INTO app_in_list ('
        . 'app_id'
        . ',' . 'app_list_id'
        . ',' . $column_position
        . ',' . $column_latitude
        . ',' . $column_longitude
        . ') VALUES ('
        . $app_id
        . ',' . $app_list_id
        . ',' . $value_position
        . ',' . $value_latitude
        . ',' . $value_longitude
        . ')' 
        . $update_string
        . ';';

      $result = $this->insert($query_string); 
      return $result;
    }

    public function removeFromAppList($app_id, $app_list_id) {
      $app_id = $this->dbh->quote($app_id);
      $app_list_id = $this->dbh->quote($app_list_id);
      
      $query_string = '
        DELETE FROM app_in_list WHERE'
        . ' app_id LIKE ' . $app_id
        . ' AND app_list_id LIKE ' . $app_list_id
        . ';';

      $result = $this->insert($query_string);
      return $result;
    }

    public function setLiveAppList($app_list_id, $launcher_id) {
      $app_list_id = $this->dbh->quote($app_list_id);
      $id = $this->dbh->quote($launcher_id);
      $query_string = '
        UPDATE launcher SET '
        . 'live_app_list_rev=' . $app_list_id
        . ' WHERE '
        . 'id LIKE ' . $id
        . ';';           
      
      $result = $this->insert($query_string);
      return $result;
    }

    public function getLiveAppListId($launcher_id) {
      $id = $this->dbh->quote($launcher_id);
      $query_string = 'SELECT * FROM launcher WHERE id LIKE ' . $id . ';';
      $result = $this->performQuery($query_string)->fetch(PDO::FETCH_OBJ);
      return $result;
    }

    public function getApp($app_id) {
      $id = $this->dbh->quote($app_id);
      $query_string = 'SELECT * FROM app WHERE id LIKE ' . $id . ';';
      $result = $this->performQuery($query_string)->fetch(PDO::FETCH_OBJ);
      return $result;     
    }

    public function addApp($data) {
      $id = $this->dbh->quote($data->id);
      $title = $this->dbh->quote($data->title);
      $developer = $this->dbh->quote($data->developer);
      $icon_url = $this->dbh->quote($data->icon_url);
      $changed = time();
      $query_string = '
        INSERT INTO app (
        id, title, developer, icon_url, changed)
        VALUES ('
        . $id
        . ',' . $title
        . ',' . $developer
        . ',' . $icon_url
        . ',' . $changed
        . ')'
        . ' ON DUPLICATE KEY UPDATE '
        . 'title=' . $title
        . ',developer=' . $developer
        . ',icon_url=' . $icon_url
        . ',changed=' . $changed
        . ';';
      $result = $this->insert($query_string);
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

    private function insert($insert_query) {
      error_log($insert_query);
      $queryResult = $this->performQuery($insert_query);
      if($queryResult) {
        $numOfChangedRows = $queryResult->rowCount();
      } else {
        $numOfChangedRows = 0;
      }
      $result = 0;
      if(0 < $numOfChangedRows) {
        $result = 1;
      }
      return $result;
    }

  }
