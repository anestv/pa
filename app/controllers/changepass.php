<?php namespace controllers;
use core\view as View;
use \Exception;

class ChangePass extends \core\controller {
  
  public function get(){
    $this->requireUser('loggedin');
    
    $data['title'] = 'Change your password';
    
    View::rendertemplate('header', $data);
    View::render('changepass', $data);
    View::rendertemplate('footer', $data);
  }
  
  public function post(){
    $this->requireUser('loggedin');
    
    try {
      
      if (isset($_POST["old"]) and trim($_POST["old"]))
        $old = $_POST["old"];
      else throw new Exception("The old password was not given");
      
      if (isset($_POST["new"]) and isset($_POST['new2']) and trim($_POST["new"]))
        $new = $_POST["new"];
      else throw new Exception("A new password was not given");
      
      if ($new !== $_POST['new2'])
        throw new Exception("The new passwords do not match");
      
      $GLOBALS['user']->changePassword($old, $new, $_POST['rand']);
      
      $GLOBALS['changePassSuccess'] = true;
      
    } catch (Exception $e) {
      self::handleException($e);
    }
    
    $this->get();
  }
}
?>
