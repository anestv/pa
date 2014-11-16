<?php namespace controllers;
use core\view as View;

class API extends \core\controller{
  
  public function profileDisplay($username){
    
    header('Content-Type: text/css', true);
    
    try {
      $user = new \models\User($username);
      
      if (! $user->isRealUser())
        throw new Exception('This user does not seem to exist.', 404);
      
      if ($user->deactivated)
        throw new Exception('This user has deactivated their account.');
      
    } catch (Exception $e) {
      
      $msg = $e->getMessage();
      
      header("X-Error-Descr: $msg");
      echo "/* Error: $msg */";
      
      return;
    }
    
    $data = $user->style;
    
    View::render('profileDisplay', $data);
  }

  public function __construct(){
    parent::__construct();
  }
  
}
?>
