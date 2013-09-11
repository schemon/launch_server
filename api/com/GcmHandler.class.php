<?php
  class GcmHandler {
    const APPLAND_PUSH_DEFAULT_GCM_AUTH_KEY = 'AIzaSyCUBj2Sv49uBDE6rJM7oVLgng3WYrpXix0';

    public function __construct() {
    } 

    public function performPush($req) {
      $push_body = $this->buildGcmRequestBody($req);
      $result = $this->performGcmRequest($push_body);
      return $result;
    }

    private function buildGcmRequestBody($req) {

      $dbHandler = new DbHandler();
      $registration_ids = $dbHandler->getGcmRegistrationIds($req->launcherId);

      $is_dry_run = FALSE;

      $collapse_key = 'LauncherPush';

      $data = new StdClass();
      $data->msg = 'Hello this is the Cloud speaking!';
      $data->CloudReq = $req->CloudReq;

      $body = array(
        'registration_ids' => $registration_ids,
        'dry_run' => $is_dry_run,
        'collapse_key' => $collapse_key,
        'data' => $data,
      );
      return json_encode($body);
    }

    private function performGcmRequest($post_body) {
      $ch = curl_init();

      $opts = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_URL      => 'https://android.googleapis.com/gcm/send',
        CURLOPT_HTTPHEADER => array(
          'Content-Type:application/json',
          'Authorization:key=' . self::APPLAND_PUSH_DEFAULT_GCM_AUTH_KEY,
        ),
        CURLOPT_POSTFIELDS => $post_body,
      );

      curl_setopt_array($ch, $opts);

      // grab response
      $curl_resp = curl_exec($ch);
      return $curl_resp;
    }
  }
