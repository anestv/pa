<?php namespace controllers;
use core\view as View;
use \Exception;
use \models\Question as Quest;

class Question extends \core\controller{
  
  public function __construct(){
    parent::__construct();
  }
  
  public function view($qid){
    try {
      $q = new Quest($qid);
      
      $q->preparePrint();
      
      if (empty($q->answer))
        throw new Exception('This question has not been answered yet.');
      
    } catch (Exception $e) {
      $this->handleException($e);
    }
    
    $data['title'] = 'View a question';
    $data['question'] = $q;
    
    View::rendertemplate('header',$data);
    View::render('viewq',$data);
    View::rendertemplate('footer',$data);
  }
  
  
  public function getReport($qid){
    $this->requireUser('loggedin');
    
    try {
      $q = new Quest($qid);
      $q->preparePrint();
      
      if (empty($q->answer))
        throw new Exception('This question has not been answered yet.');
      
      $data['title'] = 'Report a question';
      $data['styles'] = array('slideCancel.css');
      
      $data['q'] = $q;
      $data['suggestDelete'] = $GLOBALS['user']->username === $q->touser->username;
      
    } catch (Exception $e) {
      $this->handleException($e);
    }
    
    View::rendertemplate('header', $data);
    View::render('reportq', $data);
    View::rendertemplate('footer', $data);
  }
  
  
  public function postReport($qid){
    $this->requireUser('loggedin');
    
    try {
      if (empty($_POST['reason']))
        throw new Exception('Why do you want to report this question?');
      
      $q = new Quest($qid);
      $q->report($_POST['reason']);
      
      $_SESSION['reportSuccess'] = true; //TODO print a message 'thank you for letting us know'
      \helpers\Url::redirect('user/'. $q->touser->username);
    } catch (Exception $e) {
      //TODO
      $this->getReport($qid);
    }
  }
  
}
?>
