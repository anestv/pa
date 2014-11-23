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
      session_destroy();
      
      $_SESSION['deleteAccSuccess'] = true;
      \helpers\Url::redirect('');
      
    } catch (Exception $e) {
      $this->handleException($e);
      $this->get();
    }
  }
}
?>
