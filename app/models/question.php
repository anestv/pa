<?php namespace models;

class Question extends \core\model {
  
  public $qid, $question, $answer, $fromuser, $touser;
  public $timeasked, $timeanswered, $pubAsk;
  
  public function __construct($qid){
    parent::__construct();
    
    // look for it in DB
    
    // make $touser a User obj . possibly fromuser too, but that could be anonymous or deleteduser
  }
  
  public static function create($text, $pubAsk, $from, $to){
    $touser = new User($to);
    
    if (!$touser->askableBy($from))
      throw new Exception('Sorry, you cannot ask this user a question', 403);
    
    $pubAsk = ($pubAsk ? 1 : 0);
    
    // html escape question text
    // TODO insert into db
    
    
    return new self($qid);
  }
  
  public function answer($text){
    if ($loggedInUser != $this->touser) 
      throw new Exception('You cannot answer this question');
    
    // do all sorts of validation
    
    // and possibly html escaping
    $this->answer = htmlspecialchars($text);
    
    // TODO update DB entry (dont forget timeanswered = NOW())
  }
  
  public function writeOut($extended = false){
    
    // an example of extended use is in viewq
    
    if (! $this->touser->profileVisibleBy($loggedInUser))
      throw new Exception('Sorry, you do not have the right to see this question');
  }
  
  public function report($reason){
    
    if (! in_array($reason, array('illegal', 'threat', 'tos', 'porn', 'copyright', 'other')))
      throw new InvalidArgumentException("Select one of the listed reasons");
    
    if (! $this->touser->profileVisibleBy($loggedInUser))
      throw new Exception('Sorry, you do not have the right to see this question');
    
    // TODO insert into db
  }
  
  public function delete(){
    
    // note that we check for comparison, NOT identity
    // the objects must have the same properties, not same reference
    if ($loggedInUser != $this->touser) 
      throw new Exception('You cannot delete this question');
    
    // TODO delete question from DB
    
    // it would be good if we could delete $this
  }
  
}
?>
