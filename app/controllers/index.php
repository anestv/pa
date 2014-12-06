<?php namespace controllers;
use core\view as View;

class index extends \core\controller{
  
  public function index(){  
    
    $data['noGeneralCss'] = true;
    
    if (empty($_SESSION['user'])){
      $data['styles'] = ['notLoggedIn.css'];
      
      View::rendertemplate('header',$data);
      View::render('notLoggedIn',$data);
    } else {
      $data['styles'] = ['topBar.css'];
      
      $data['unseen'] = $GLOBALS['user']->getUnseen();
      $data['username'] = $GLOBALS['user']->username;
      
      View::rendertemplate('header',$data);
      View::render('index', $data);
    }
    
    View::rendertemplate('footer',$data);
  }
}
?>
