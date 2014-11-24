<?php namespace controllers;
use core\view as View;
use \Exception;

class Pending extends \core\controller {
  
  public function get(){
    $this->requireUser('loggedin');
    
    try {
      $user = $GLOBALS['user']->username;
      $query = "SELECT * FROM questions WHERE touser = '$user'".
        "AND answer IS NULL ORDER BY timeasked LIMIT 50;";
      /* Older questions will show up first. This is to encourage users to
         answer old questions, or delete them instead of letting them be. */
      
      $qs = new \models\QuestionSet($query);
      
      $data['title'] = 'Pending questions';
      $data['questions'] = $qs->members;
      $data['count'] = count($qs->members);
      
    } catch (Exception $e) {
      $this->handleException($e);
    }
    
    View::rendertemplate('header', $data);
    View::render('pending', $data);
    View::rendertemplate('footer', $data);
  }
}
?>
