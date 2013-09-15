<?php

class AppListHandler {
  
  public function newList($req) {
    $dbHandler = new DbHandler();
    return $dbHandler->createNewAppList($req->launcherId, $req->platform);
  }

  public function deleteList($req) {
    $dbHandler = new DbHandler();
    return $dbHandler->deleteList($req->revision, $req->launcherId);
  }

  public function getListAll($req) {
    $dbHandler = new DbHandler();
    return $dbHandler->getAppListAll($req->launcherId);
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

