<?php namespace core;

class Config {
  
  public function __construct(){
    
    //turn on output buffering
    ob_start();
    
    //start sessions
    \helpers\session::init();
  
    set_exception_handler('core\logger::exception_handler');
    set_error_handler('core\logger::error_handler');
  
    //set timezone
    date_default_timezone_set('Europe/Athens');
    
    $sec = parse_ini_file('secrets.ini');
  
    //site address
    define('BASE_DIR', $sec['baseDir']);
    define('DIR','http://'.$_SERVER['HTTP_HOST'].BASE_DIR);
    
    define('DB_HOST', $sec['address']);
    define('DB_NAME', $sec['database']);
    define('DB_USER', $sec['username']);
    define('DB_PASS', $sec['password']);
    
    //set prefix for sessions
    define('SESSION_PREFIX','pa_');
    
    //optionall create a constant for the name of the site
    define('SITETITLE','PrivateAsk');
    
    //Facebook app id
    define('FACEBOOK_APP_ID', $sec['fbAppId']);
    
    define("RECAPTCHA_SITEKEY",$sec['recaptchaSitekey']);
    define("RECAPTCHA_SECRET", $sec['recaptchaSecret']);
    
    define('CONTACT_URL', $sec['contactUrl']);
    
    //set the default template
    \helpers\session::set('template','default');
    
    \helpers\MyFB::init($sec['fbAppSecret']);
  }
  
}