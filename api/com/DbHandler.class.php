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

     private function performQuery($queryString) {
       return $this->dbh->query($queryString);
     }
  }
