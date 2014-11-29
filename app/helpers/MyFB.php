<?php namespace helpers;

class MyFB {
  
  static $facebook;
  
  public static function init($secret){
    
    \Facebook\FacebookSession::setDefaultApplication(FACEBOOK_APP_ID, $secret);
    
    self::$facebook = new \Facebook\FacebookRedirectLoginHelper(DIR .'api/facebooklogin');
  }
  
  public static function getLoginUrl(){
    
    return self::$facebook->getLoginUrl();
  }
  
  function getStuff($sessid){
    
    $session = new \Facebook\FacebookSession($sessid);
    $request = new \Facebook\FacebookRequest($session, 'GET', '/me');
    
    $res = $request->execute()->getGraphObject()->asArray();
    
    return $res;
  }
}
