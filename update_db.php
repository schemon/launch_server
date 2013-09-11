<?php
  
  $user = 'root';
  $pass = 'FrukE2pE';
  $dbh = new PDO('mysql:host=launcherdb.cpu4kl1cdbe3.eu-west-1.rds.amazonaws.com;port=3306;dbname=launcher', $user, $pass);
   
  $result = $dbh->query(
    'CREATE TABLE IF NOT EXISTS app_list (
      id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
      owner VARCHAR(255) NOT NULL,
      position INT DEFAULT -1,
      package_name VARCHAR(255) NOT NULL,
      title VARCHAR(255),
      icon_url VARCHAR(255));'
  );
  var_dump($result);


  $result = $dbh->query(
    'CREATE TABLE IF NOT EXISTS device (
      id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
      device_token VARCHAR(255) NOT NULL,
      launcher_id VARCHAR(255) NOT NULL,
      android_id VARCHAR(255) NULL,
      gcm_registration_id VARCHAR(255));'
  );
  var_dump($result);

  $result = $dbh->query(
    'ALTER TABLE device ADD UNIQUE (device_token)');
  var_dump($result);

  $result = $dbh->query(
    'CREATE TABLE IF NOT EXISTS launcher (
      id VARCHAR(255) PRIMARY KEY);'
  );
  var_dump($result);

