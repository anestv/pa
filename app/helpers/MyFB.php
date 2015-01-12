<?php namespace helpers;

class MyFB {
  
  static $facebook;
  
  public static function setPath($url){
    self::$facebook = new \Facebook\FacebookRedirectLoginHelper(DIR .$url);
    return self::$facebook;
  }
  
  public static function getLoginUrl(){
    return self::$facebook->getLoginUrl();
  }
  
  public static function getStuff($sessid){
    
    $session = new \Facebook\FacebookSession($sessid);
    $request = new \Facebook\FacebookRequest($session, 'GET', '/me');
    
    return $request->execute()->getGraphObject()->asArray();
  }
}
