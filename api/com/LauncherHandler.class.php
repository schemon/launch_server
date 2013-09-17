<?php

class LauncherHandler {

  public function setLiveAppList($app_list_id, $launcher_id) {
    $dbHandler = new DbHandler();
    $new_data = (object) array(
      'has_been_live' => 1,
      'changed' => time(),
    );
    $dbHandler->updateList($app_list_id, $new_data);
    $result = $dbHandler->setLiveAppList($app_list_id, $launcher_id);
    return $result;
  }

}
