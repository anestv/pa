<?php namespace controllers;
use core\view as View;
use \Exception;

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
  
  public function load($owner){
    
    try {
      if (empty($owner) or !isset($_GET['offset']))
        throw new Exception('Required parameters were not provided');
      
      if ((!is_numeric($_GET['offset'])) or ($_GET['offset'] < 0))
        throw new InvalidArgumentException('The offset is not of correct type.');
      $offset = intval($_GET['offset']);
      
      $result = \models\LoadQ::main($owner, $offset); //prints question after checks and returns $res
      
      if ($result->num_rows < 11)
        echo '<div data-last="1"></div>';
      
    } catch (Exception $e) {
      $this->handleException($e);
    }
  }
  
}
?>
