<?php namespace models;

use \RuntimeException;

class QuestionSet extends \core\model {
  
  public $members = [];
  protected $query; // could be used for debugging
  
  public function __construct($query){
    parent::__construct();
    $this->query = $query;
    
    /* Instead of calling new Question($qid) n times thus making 3n
    queries to the database (we also call new User), we can just make
    one and fill the Question's properties from here. A problem is
    we're still making n queries as we call new User */
    
    $res = self::$_db->query($query);
    
    if (!$res) throw new RuntimeException(self::$_db->error);
    
    while ($i = $res->fetch_array()){
      
      $q = new Question;
      
      $q->qid = $i['id'];
      $q->question = $i['question'];
      $q->answer = $i['answer'];
      $q->timeasked = $i['timeasked'];
      $q->timeanswered = $i['timeanswered'];
      $q->pubAsk = $i['publicasker'];
      $q->fromuser = (object) ['username' => $i['fromuser']];
      $q->touser = new User($i['touser']);
      
      $this->members[] = $q;
    }
  }
}
