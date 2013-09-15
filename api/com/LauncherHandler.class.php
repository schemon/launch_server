<?php

class LauncherHandler {

  public function setLiveAppList($app_list_id, $launcher_id) {
    $dbHandler = new DbHandler();
    $result = $dbHandler->setLiveAppList($app_list_id, $launcher_id);
    return $result;
  }

}
