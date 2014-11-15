<?php namespace controllers;
use core\view as View;

class Statics extends \core\controller {
  
  public function __construct(){
    parent::__construct();
  }
  
  public function help(){
    $data['title'] = 'FAQ';
    $data['styles'] = array('help.css');
    $data['noGeneralCss'] = true;
    
    View::rendertemplate('header', $data);
    View::render('help', $data);
    View::rendertemplate('footer', $data);
  }
  
  public function terms(){
    $data['title'] = 'Terms and Conditions';
    $data['styles'] = array('terms.css');
    $data['noGeneralCss'] = true;
    
    View::rendertemplate('header', $data);
    View::render('terms', $data);
    View::rendertemplate('footer', $data);
  }
}
?>
