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
       $apps = $this->dbh->query('select * from app_list;')->fetchAll(PDO::FETCH_OBJ);
       foreach($apps as $app) {
         $appTile = new StdClass();
         $appTile->package_name = $app->package_name;
         $appTile->position = $app->position;
         $appTile->title = $app->title;
         $appTile->icon_url = $app->icon_url;

         $result[] = $appTile;
       }
       return $result; 
     }  
  }
