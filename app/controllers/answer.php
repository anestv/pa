<?php namespace controllers;
use core\view as View;
use \models\Question as Question;
use \Exception;

class Answer extends \core\controller {
  
  public function get($qid){
    $this->requireUser('loggedin');
    
    try {
      $q = new Question($qid);
      
      if ($q->touser->username != $GLOBALS['user']->username)
        throw new Exception('You cannot answer this question', 403);
      
      if ($q->answer)
        throw new Exception('You have already answered this question', 405);
    } catch (Exception $e) {
      $this->handleException($e);
    }
    
    $data['title'] = 'Answer a question';
    $data['q'] = $q;
    
    View::rendertemplate('header', $data);
    View::render('answer', $data);
    View::rendertemplate('footer', $data);
  }
  
  public function post($qid){
    $this->requireUser('loggedin');
    
    try {
      $q = new Question($qid);
      
      if (!(isset($_POST['answer']) and trim($_POST['answer'])))
        throw new Exception('Enter an answer to the question');
      
      // other checks are done in the model (Question)
      
      $q->answer($_POST['answer']);
      
      $_SESSION['answerSuccess'] = $qid;
      \helpers\Url::redirect('pending');
      
    } catch (Exception $e) {
      $this->handleException($e);
      $this->get($qid);
    }
  }
}
