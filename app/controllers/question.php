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
  
}
?>