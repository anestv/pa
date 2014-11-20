<?php namespace controllers;
use core\view as View;
use \Exception, \InvalidArgumentException, \RuntimeException;

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
  
  public function friends(){
    
    $this->requireUser('loggedin');
    
    $possVals = ['set', 'add', 'remove'];
    
    try {
      if (!(isset($_POST['do']) and in_array($_POST['do'], $possVals)))
        throw new InvalidArgumentException('Parameter do was not valid', 400);
      
      if (!(isset($_POST['friends']) and trim($_POST['friends'])))
        throw new InvalidArgumentException('Parameter friends was not given', 400);
      
      $GLOBALS['user']->editFriends($_POST['do'], $_POST['friends']);
      
      // do not print anything, this page will be used with AJAX
      // or in a way that it wont be visible to the user
      echo 'Success!'; // just a small indicator
      
    } catch (Exception $e){
      $this->handleException($e);
    }
  }
  
  public function ask(){
    
    try {
      
      if (!(isset($_POST['question']) and trim($_POST['question'])))
        throw new Exception('You did not enter a question', 400);
      
      if (empty($_POST['to']))
        throw new Exception('Required parameters were not provided', 400);
      
      $q = \models\Question::create($_POST['question'], $_POST['pubAsk'], $GLOBALS['user'], $_POST['to']);
      
      echo 'Success!';
      
      if ($this->byAJAX){
        http_response_code(201); // 201 = Created
        Header('Location: '. DIR ."question/$q->qid"); // won't actually redirect
      } else {
        $_SESSION['questionSent'] = true;
        \helpers\Url::redirect('user/'.$q->touser->username);
      }
      
    } catch (Exception $e) {
      $this->handleException($e);
      echo $e->getMessage();
    }
  }
  
}
?>
