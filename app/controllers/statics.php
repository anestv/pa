<?php namespace controllers;
use core\view as View;

class Statics extends \core\controller {
  
  public function help(){
    $data['title'] = 'FAQ';
    $data['styles'] = ['help.css'];
    $data['noGeneralCss'] = true;
    
    View::rendertemplate('header', $data);
    View::render('help', $data);
    View::rendertemplate('footer', $data);
  }
  
  public function terms(){
    $data['title'] = 'Terms and Conditions';
    $data['styles'] = ['terms.css'];
    $data['noGeneralCss'] = true;
    
    View::rendertemplate('header', $data);
    View::render('terms', $data);
    View::rendertemplate('footer', $data);
  }
}
?>
