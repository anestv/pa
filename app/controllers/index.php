<?php namespace controllers;
use core\view as View;

class index extends \core\controller{

  public function __construct(){
    parent::__construct();
  }
  
  public function index(){  
      
    if (empty($_SESSION['user'])){
      View::render('notLoggedIn',$data);
    } else {
      
      $data['unseen'] = $this->user->getUnseen();
      $data['user'] = $this->user;
      
      //View::rendertemplate('header',$data);
      View::render('index',$data);
      //View::rendertemplate('footer',$data);
    }
  }
  
}
