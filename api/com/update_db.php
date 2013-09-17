<?php
  
  $user = 'root';
  $pass = 'FrukE2pE';
  $dbh = new PDO('mysql:host=launcherdb.cpu4kl1cdbe3.eu-west-1.rds.amazonaws.com;port=3306;dbname=launcher', $user, $pass);
   
  $result = $dbh->query(
    'CREATE TABLE IF NOT EXISTS app_list (
      id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
      launcher_id VARCHAR(255) NOT NULL,
      platform VARCHAR(255),
      has_been_live TINYINT(1) DEFAULT 0,
      changed INT DEFAULT 0);'
  );
  var_dump($result);

  $result = $dbh->query(
    'CREATE TABLE IF NOT EXISTS app (
      id VARCHAR(255) PRIMARY KEY,
      title VARCHAR(255),
      developer VARCHAR(255),
      icon_url VARCHAR(255),
      changed INT DEFAULT 0);'
  );
  var_dump($result);

  $result = $dbh->query(
    'CREATE TABLE IF NOT EXISTS app_in_list (
      app_in_list_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
      app_list_id INT NOT NULL,
      app_id VARCHAR(255) NOT NULL,
      position INT DEFAULT -1,
      latitude VARCHAR(255),
      longitude VARCHAR(255),
      FOREIGN KEY (app_list_id) REFERENCES app_list(id) ON DELETE CASCADE,
      FOREIGN KEY (app_id) REFERENCES app(id) ON DELETE RESTRICT,
      CONSTRAINT app_list_id_app_id UNIQUE (app_list_id,app_id));'
  );
  var_dump($result);

  $result = $dbh->query(
    'CREATE TABLE IF NOT EXISTS device (
      id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
      device_token VARCHAR(255) UNIQUE,
      launcher_id VARCHAR(255) NOT NULL,
      android_id VARCHAR(255) NULL,
      gcm_registration_id VARCHAR(255),
      active_app_list INT
    );'
  );
  var_dump($result);
  
  $result = $dbh->query(
    'CREATE TABLE IF NOT EXISTS launcher (
      id VARCHAR(255) PRIMARY KEY,
      live_app_list_rev INT,
      odpl_id VARCHAR(255),
      FOREIGN KEY (live_app_list_rev) 
        REFERENCES app_list(id) ON DELETE RESTRICT 
    );'
  );
  var_dump($result);
  
  $result = $dbh->query(
    'CREATE TABLE IF NOT EXISTS admin (
      id VARCHAR(255) PRIMARY KEY,
      launcher_id VARCHAR(255), 
      token VARCHAR(255),
      changed INT
    );'
  );
  var_dump($result);

