<?php namespace core;
use core\config as Config;
use core\view as View;
use core\error as Error;

class Controller {
  
  protected $byAJAX;
  
  public function __construct(){
    $this->byAJAX = isset(apache_request_headers()['X-Requested-With']) and 
      apache_request_headers()['X-Requested-With'] === "XMLHttpRequest";
  }

  //Display an error page if nothing exists
  protected function _error($error) {
    require 'app/core/error.php';
    $this->_controller = new error($error);
    $this->_controller->index();
    die;
  }
  
  protected function requireUser($state = 'loggedin'){
    if ($state == 'loggedin'){
      if (! $GLOBALS['user']->isRealUser()){
        $_SESSION['requiredLogin'] = Router::$requestedRelPath;
        
        \helpers\Url::redirect('login');
      }
    } else
      if ($GLOBALS['user']->isRealUser()){
        $_SESSION['alreadyLoggedIn'] = true;
        
        \helpers\Url::redirect('');
      }
  }
  
  protected static function errorMessage($message = ''){
    Logger::errorMessage($message);
    // prints view error/error and exits
  }
  
  protected static function handleException($e, $header = ''){
    
    if ($e->getCode())
      http_response_code($e->getCode());
    else if ($e instanceof RuntimeException)
      http_response_code(500);
    else
      http_response_code(400);
    
    $excMsg = $e->getMessage();
    header("X-Error-Descr: $excMsg");
    
    if ($header)
      $GLOBALS['warnMessage'] .= "<p class=\"header\">$header</p>";
    
    $GLOBALS['warnMessage'] .= "<p>$excMsg</p>";
  }

}
