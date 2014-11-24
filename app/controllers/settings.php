<?php namespace controllers;
use core\view as View;

class Settings extends \core\controller {
  
  public function get(){
    $this->requireUser('loggedin');
    
    $data['title'] = 'Settings';
    $data['styles'] = ['settings.css', "/api/profileDisplay/".$GLOBALS['user']->username];
    $data['scripts'] = ['jquery' => 1, 'semantic' => 1];
    $data['scripts']['custom'] = ['settings.js'];
    
    $data['u'] = $GLOBALS['user']->raw;
    
    View::rendertemplate('header', $data);
    View::render('settings', $data);
    View::rendertemplate('footer', $data);
  }
  
  public function post(){
    $this->requireUser('loggedin');
    
    // keep only keys present in User->raw
    $settings = array_intersect_key($_POST, $GLOBALS['user']->raw);
    
    $GLOBALS['warnMessage'] = $GLOBALS['user']->editSettings($settings);
    
    $this->get();
  }
}
?>
