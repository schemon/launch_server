<?php

  class RequestHandler {

    public function handle($req) {

      if(isset($req->ListReq)) {
        $dbHandler = new DbHandler();
        $appList = $dbHandler->fetchApplist($req->ListReq->launcherId);
        $response = array(
          'ListResp' => array(
            'revision' => 1,
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

