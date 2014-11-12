<?php namespace core;
use core\config as Config;
use core\view as View;
use core\error as Error;

class Controller {
  
  public $view;
  
  public function __construct(){
    //initialise the views object
    $this->view = new view();
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
      if (! $GLOBALS['user']->isRealUser())
        \helpers\Url::redirect('login');
    } else
      if ($GLOBALS['user']->isRealUser())
        \helpers\Url::redirect('');
  }
  
  static function handleException($e, $header = ''){
    if ($e instanceof RuntimeException)
      http_response_code(500);
    else http_response_code(400);
    
    $excMsg = $e->getMessage();
    header("X-Error-Descr: $excMsg");
    
    if ($header)
      $GLOBALS['warnMessage'] .= "<p class=\"header\">$header</p>";
    
    $GLOBALS['warnMessage'] .= "<p>$excMsg</p>";
  }

}
