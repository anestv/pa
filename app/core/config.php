<?php namespace core;

class Config {
  
  public function __construct(){
    
    //turn on output buffering
    ob_start();
    
    //start sessions
    \helpers\session::init();
  
    set_exception_handler('core\logger::exception_handler');
    set_error_handler('core\logger::error_handler');
    
    $sec = parse_ini_file('secrets.ini');
    
    date_default_timezone_set($sec['timezone']);
    
    //site address
    define('BASE_DIR', $sec['baseDir']);
    define('DIR','http://'.$_SERVER['HTTP_HOST'].BASE_DIR);
    
    define('DB_HOST', $sec['address']);
    define('DB_NAME', $sec['database']);
    define('DB_USER', $sec['username']);
    define('DB_PASS', $sec['password']);
    
    //set prefix for sessions
    define('SESSION_PREFIX','pa_');
    
    define('SITETITLE','PrivateAsk');
    
    if ($sec['fbAppId'] || $sec['fbAppSecret']){ // at least one is defined
      define('ENABLE_FACEBOOK', true);
      \Facebook\FacebookSession::setDefaultApplication($sec['fbAppId'], $sec['fbAppSecret']);
    } else
      define('ENABLE_FACEBOOK', false);
    
    if ($sec['recaptchaSitekey'] || $sec['recaptchaSecret']){
      define('ENABLE_CAPTCHA', true);
      define('RECAPTCHA_SITEKEY',$sec['recaptchaSitekey']);
      define('RECAPTCHA_SECRET', $sec['recaptchaSecret']);
    } else
      define('ENABLE_CAPTCHA', false);
    
    define('CONTACT_URL', $sec['contactUrl']);
    
    //set the default template
    \helpers\session::set('template','default');
  }
}
