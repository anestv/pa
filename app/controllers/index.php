<?php namespace controllers;
use core\view as View;

class index extends \core\controller{
  
  public function index(){  
    
    if (empty($_SESSION['user'])){
      $this->notLoggedIn();
      return;
    }
    
    $data['noGeneralCss'] = true;
    
    View::rendertemplate('header',$data);
    View::render('index', $data);
    View::rendertemplate('footer',$data);
  }
  
  private function notLoggedIn(){
    
    $data['noGeneralCss'] = true;
    $data['styles'] = ['notLoggedIn.css'];
    
    $data['fbLoginUrl'] = \helpers\MyFB::$facebook->getLoginUrl();
    
    View::rendertemplate('header', $data);
    View::render('notLoggedIn', $data);
    View::rendertemplate('footer', $data);
  }
}
?>
