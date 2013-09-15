<?php

  class RequestHandler {

    public function handle($req) {

      if(isset($req->ListReq)) {
        $launcher_id = $req->ListReq->launcherId; 
        $dbHandler = new DbHandler();
        if(isset($req->ListReq->revision)) {
          $app_list_id = $req->ListReq->revision;
        } else { 
          $launcher = $dbHandler->getLiveAppListId($launcher_id);
          $app_list_id = $launcher->live_app_list_rev;
        }

        $appList = $dbHandler->fetchApplist($app_list_id);
        $response = array(
          'ListResp' => array(
            'revision' => $app_list_id,
            'tile' => $appList
          )
        );
      } else if(isset($req->SendInfoReq)) {
        $deviceHandler = new DeviceHandler();
        $result = $deviceHandler->handle($req->SendInfoReq);
        $response = array(
          'SendInfoResp' => array(
            'result' => $result,
          ),
        );
      } else if(isset($req->PerformPushReq)) {
        $gcmHandler = new GcmHandler();
        $result = $gcmHandler->performPush($req->PerformPushReq);
        $response = array(
          'PerformPushResp' => array(
            'result' => $result,
          )
        );
      } else if(isset($req->CrawlAppReq)) {
        $package_name = $req->CrawlAppReq->packageName;
        $crawler = new GooglePlayCrawler();
        $result = $crawler->crawlApp($package_name);
        $response = array(
          'CrawlAppResp' => array(
            'result' => $result,
          )
        );
      } else if(isset($req->AddToListReq)) {
        $appListHandler = new AppListHandler();
        $result = $appListHandler->addApp($req->AddToListReq); 
        $response = array(
          'AddToListResp' => array(
            'result' => $result,
          )
        );
      } else if(isset($req->RemoveFromListReq)) {
        $appListHandler = new AppListHandler();
        $result = $appListHandler->removeApp($req->RemoveFromListReq);
        $response = array(
          'RemoveFromListResp' => array(
            'result' => $result,
          )
        );
      } else if(isset($req->NewListRevisionReq)) {
        $appListHandler = new AppListHandler();
        $result = $appListHandler->newList($req->NewListRevisionReq);
        $response = array(
          'NewListRevisionResp' => array(
            'result' => $result,
          )
        );
      } else if(isset($req->DeleteListRevisionReq)) {
        $appListHandler = new AppListHandler();
        $result = $appListHandler->deleteList($req->DeleteListRevisionReq);
        $response = array(
          'DeleteListRevisionResp' => array(
            'result' => $result,
          )
        );
      } else if(isset($req->ListRevisionReq)) {
        $appListHandler = new AppListHandler();
        $result = $appListHandler->getListAll($req->ListRevisionReq);
        $response = array(
          'ListRevisionResp' => array(
            'result' => $result,
          )
        );
      } else if(isset($req->PublishListReq)) {
        $launcher_id = $req->PublishListReq->launcherId;
        $app_list_id = $req->PublishListReq->appListId;
        $launcherHandler = new LauncherHandler();
        $result = $launcherHandler->setLiveAppList($app_list_id, $launcher_id);
        
        if($result) {
          $gcmHandler = new GcmHandler();
          $push_req = (object) array(
            'launcherId' => $launcher_id,
            'CloudReq' => (object) array(
              'AppListUpdateInfo' => (object) array(
                'revision' => $app_list_id,
              ),
            ),
          );   
          $push_was_sent = $gcmHandler->performPush($push_req);
        } else {
          $push_was_sent = 0;
        }
        $response = array(
          'PublishListReq' => array(
            'result' => $result,
            'pushWasSent' => $push_was_sent,
          )
        );
      } else {
       $response = array(
          'ErrorResp' => array(
            'result' => 'Unknown request',
          ),
        );
      }
      $response = json_encode($response);
      error_log(var_export($response, TRUE));
      return $response;
    }

  }

