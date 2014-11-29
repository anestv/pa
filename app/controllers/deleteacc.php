<?php namespace controllers;
use core\view as View;
use \Exception;

class DeleteAcc extends \core\controller {
  
  public function get(){
    $this->requireUser('loggedin');
    
    $data['title'] = 'Delete your account';
    $data['styles'] = ['slideCancel.css'];
    
    View::rendertemplate('header', $data);
    View::render('deleteacc', $data);
    View::rendertemplate('footer', $data);
  }
  
  public function post(){
    $this->requireUser('loggedin');
    
    try {
      $GLOBALS['user']->deleteAccount($_POST['user'], $_POST['pass']);
      
      session_unset(); //unset all session variables
      session_regenerate_id(true);
      // not session_destroy so that we can set $_SESSION['deleteAccSuccess']
      
      $_SESSION['deleteAccSuccess'] = true;
      \helpers\Url::redirect('');
      
    } catch (Exception $e) {
      self::handleException($e);
      $this->get();
    }
  }
}
?>
