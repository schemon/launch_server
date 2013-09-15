<?php

class GooglePlayCrawler {

  public static function crawlApp($package_name) {
    $url = 'https://play.google.com/store/apps/details?id=' . $package_name;
    $htmlStr = file_get_contents($url);

    $start = '<div class="details-wrapper apps square-cover" data-docid="'.$package_name.'">';
    $end = 'class="document-subtitle"';
    $coreStr = self::sliceString($start, $end, $htmlStr);

    $start_title = '<div class="document-title" itemprop="name"> <div>';
    $end_title = '<\/div>';
    $title = self::sliceString($start_title, $end_title, $coreStr);

    $start_icon = '<img class="cover-image" src="';
    $end_icon = '=';
    $icon_url = self::sliceString($start_icon, $end_icon, $coreStr);

    $developer = self::sliceString(
      'href="\/store\/apps\/developer\?id=',
      '"',
      $coreStr);

    if(NULL != $title) {
      $result = (object) array(
        'id' => $package_name,
        'title' => $title,
        'icon_url' => $icon_url,
        'developer' => $developer,
      );
      // Update local db with app
      $dbHandler = new DbHandler();
      if(!$dbHandler->addApp($result)) {
        $result = FALSE;
      }
    } else {
      $result = FALSE;
    }

    return $result;
  }

  private static function sliceString($start, $end, $subject) {
    $pattern = "/$start(.*?)$end/";
    preg_match($pattern, $subject, $matches);
    return $matches[1];
  }

}
