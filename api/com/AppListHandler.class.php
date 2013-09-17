<?php

class AppListHandler {
  
  public function newList($req) {
    $dbHandler = new DbHandler();
    $new_list = $dbHandler->createNewAppList($req->launcherId, $req->platform);
    if(isset($req->baseList)) {
      $apps = $dbHandler->fetchAppList($req->baseList);
      $transfered_apps = $this->addAppMultiple($apps, $new_list['list_id']);
      $new_list['transferedApps'] = $transfered_apps;
    }
    return $new_list;
  }

  public function deleteList($req) {
    $dbHandler = new DbHandler();
    return $dbHandler->deleteList($req->revision, $req->launcherId);
  }

  public function getListAll($req) {
    $dbHandler = new DbHandler();
    $result = new StdClass();
    $result->lists = $dbHandler->getAppListAll($req->launcherId);
    $result->liveList = $dbHandler->getLiveAppListId($req->launcherId);
    return $result;
  }

  public function addAppMultiple($apps, $list_id) {
    $result = true;
    foreach($apps as $key => $app) {
      $app_data = (object) array(
         'appId' =>  $app->appTile->packageName,
         'appListId' => $list_id,
         'position' => $app->appTile->position,
         'latitude' => $app->appTile->latitude,
         'longitude' => $app->appTile->longitude,
         );
      error_log(var_export($app_data, true));
      if(!$this->addApp($app_data)) {
        $result = false;
      }
    }
    return $result;
  }

  public function addApp($req_data) {
    $dbHandler = new DbHandler();
    $app_saved = $dbHandler->getApp($req_data->appId);

    // If the app was not saved try to crawl it
    if(!$app_saved) {
      $crawler = new GooglePlayCrawler();
      $app_saved = $crawler->crawlApp($req_data->appId);
    }

    error_log(var_export($app_saved, TRUE));

    // if the app could not be crawled, return error.
    if(!$app_saved) {
      $result = 'Could not find app with id: ' . $req_data->appId;
    } else {
      $result = $dbHandler->addToAppList(
        $req_data->appId,
        $req_data->appListId,
        $req_data->position,
        $req_data->latitude,
        $req_data->longitude);
    }
    if($result) {
      $new_data = array(
        'changed' => time(),
      );
      $dbHandler->updateList($req_data->appListId, $new_data);
    }
    return $result;
  }
  
  public function removeApp($req_data) {
    $dbHandler = new DbHandler();
    $result = $dbHandler->removeFromAppList(
      $req_data->appId, 
      $req_data->appListId);
    return $result;
  }



}

