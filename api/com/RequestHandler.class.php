<?php

  class RequestHandler {

    public function handle($req) {

      if(isset($req->ListReq)) {
        $dbHandler = new DbHandler;
        $appList = $dbHandler->fetchApplist($req->ListReq->launcherId);
        $response = array(
          'ListResp' => array(
            'tile' => $appList
          )
        );
      } else if(isset($req->SendInfoReq)) {
        $response = array(
          'SendInfoResp' => array(
            'result' => '0',
          ),
        );
      } else {
       $response = array(
          'ErrorResp' => array(
            'result' => 'Unknown request',
          ),
        );
      }
      return json_encode($response);
    }

  }

