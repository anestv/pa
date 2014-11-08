<?php namespace models;

use \models\User as User;

class Question extends \core\model {
  
  public $qid, $question, $answer, $fromuser, $touser;
  public $timeasked, $timeanswered, $pubAsk;
  
  public function __construct($qid){
    parent::__construct();
    
    if (!is_numeric($qid) or $qid < 0)
      throw new InvalidArgumentException('qid is not a positive integer');
    else $qid = intval($qid);
    
    $query = "SELECT * FROM questions WHERE id = $qid;";
    $res = $this->_db->query($query);
    
    if (! $res) throw new RuntimeException($this->_db->error);
    if ($res->num_rows < 1) throw new Exception("Question #$qid not found");
    
    $q = $res->fetch_array();
    
    $this->touser = new User($q['touser']);
    
    if ($this->touser->deactivated)
      throw new Exception('The owner of this question has deactivated their account.');
    
    $this->qid = $qid;
    $this->question = $q['question'];
    $this->answer = $q['answer'];
    $this->timeasked = $q['timeasked'];
    $this->timeanswered = $q['timeanswered'];
    $this->pubAsk = $q['pubAsk'];
    $this->fromuser = new User($q['fromuser']);
  }
  
  public static function create($text, $pubAsk, $from, $to){
    $touser = new User($to);
    
    // ensure proper case (because == is case sensitive)
    $to = $touser->username;
    $from = new User($from)->username;
    
    if (!$touser->askableBy($fromuser))
      throw new Exception('Sorry, you cannot ask this user a question', 403);
    
    $pubAsk = ($pubAsk ? 1 : 0);
    
    if ($from == User::NOT_LOGGED_IN) {
      $from = User::ANONYMOUS;
      $pubAsk = 0;
    } else if ($from == $to)
      $pubAsk = 1;
    
    $question = $this->_db->real_escape_string(htmlspecialchars($text));
    
    $query = "INSERT INTO questions (fromuser, touser, question, publicasker)".
      " VALUES ('$from', '$to', '$question', $pubAsk);";
    
    $res = $this->_db->query($query);
    if (! $res) throw new RuntimeException($this->_db->error);
    
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
  
  public function writeOut($extended = false, $stopIfUnanswered = true){
    
    // an example of extended use is in viewq
    
    if ($stopIfUnanswered and empty($this->answer))
      throw new Exception('This question has not been answered yet');
    
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
    
    $del = $con->query("DELETE FROM questions WHERE id = $this->qid;");
    if (!$del)
      throw new RuntimeException($con->error);
    
    // it would be good if we could delete $this
  }
  
}
?>
