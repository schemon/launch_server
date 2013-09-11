<?php
class DeviceHandler {
  
  public static function handle($req) {
    $result = 0;
    if(isset($req->DeviceInfo)) {
      $dbHandler = new DbHandler();
      $result = $dbHandler->saveDeviceInfo($req->DeviceInfo);
    } else {
      $result = 'Unknown request';
    }
    return $result;
  }
}
