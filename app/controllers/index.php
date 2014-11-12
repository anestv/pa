<?php namespace controllers;
use core\view as View;

class index extends \core\controller{

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){  
      
    $data['noGeneralCss'] = true;
    $data['styles'][] = 'index.css';
    View::rendertemplate('header',$data);
    
    if (empty($_SESSION['user'])){
      View::render('notLoggedIn',$data);
    } else {
      $data['unseen'] = $GLOBALS['user']->getUnseen();
      $data['user'] = $GLOBALS['user'];
      
      View::render('index', $data);
    }
    
    View::rendertemplate('footer',$data);
  }
  
}
