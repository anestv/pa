<?php namespace models;

use \models\User as User;
use \Exception, \RuntimeException, \InvalidArgumentException;

class Question extends \core\model {
  
  public $qid, $question, $answer, $fromuser, $touser;
  public $timeasked, $timeanswered, $pubAsk;
  
//  protected static $stmtInit = self::$_db->prepare("SELECT * FROM questions WHERE id = ?;");
  
  public function __construct($qid){
    parent::__construct();
    
    if (!is_numeric($qid) or $qid < 0)
      throw new InvalidArgumentException('qid is not a positive integer');
    else $qid = intval($qid);
    
    $query = "SELECT * FROM questions WHERE id = $qid;";
    $res = $this->_db->query($query);
//    self::$stmtInit->bind_param('i', $qid);
//    self::$stmtInit->execute();
//    self::$stmtInit->bind_result($TODO);//TODO $this->properties h locals opou xreiazetai px touser, fromuser, deleteon
//    self::$stmtInit->fetch()
      
//    TODO
    if (! $res) throw new RuntimeException($this->_db->error);
    if ($res->num_rows < 1) throw new Exception("Question #$qid not found", 404);
    
    $q = $res->fetch_array();
    
    $this->touser = new User($q['touser']);
    
    if ($this->touser->deactivated)
      throw new Exception('The owner of this question has deactivated their account.');
    
    $this->qid = $qid;
    $this->question = $q['question'];
    $this->answer = $q['answer'];
    $this->timeasked = $q['timeasked'];
    $this->timeanswered = $q['timeanswered'];
    $this->pubAsk = $q['publicasker'];
    $this->fromuser = new User($q['fromuser']);
  }
  
  public static function create($text, $pubAsk, $from, $to){
    $touser = new User($to);
    
    // ensure proper case (because == is case sensitive)
    $to = $touser->username;
    $from = (new User($from))->username;
    
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
    if ($GLOBALS['user']->username != $this->touser->username) 
      throw new Exception('You cannot answer this question', 403);
    
    if (! empty($this->answer))
      throw new Exception("You have already answered this question; you can't reanswer it", 405);
    
    $answer = $this->answer = htmlspecialchars($text);
    
    $query = "UPDATE questions SET answer = '$answer', timeanswered = NOW() WHERE id = $qid;";
    $res = $this->_db->query($query);
    
    if (! $res) throw new RuntimeException($this->_db->error);
    
    return $res;
  }
  
  public function preparePrint(){
    if (! $this->touser->profileVisibleBy($GLOBALS['user']))
      throw new Exception('Sorry, you do not have the right to see this question');
  }
  
  public function writeOut($extended = false, $partial = false){
    
    // an example of extended use is in viewq
    
    if (! $this->touser->profileVisibleBy($GLOBALS['user']))
      throw new Exception('Sorry, you do not have the right to see this question');
    
    // using closures so that $this is available inside them
    $prUser = function ($prop){
      $u = $this->$prop->username;
      return '<a href="user/'.$u.'">'.$u.'</a>';
    };
    $prDate = function ($prop){
      $time = strtotime($this->$prop);
      $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
      return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
    };
    
    echo '<div class="question"><div class="ui top attached tiny header">';
    if ($extended)
      echo 'To: '.$prUser('touser').'<a class="date">Asked: '.$prDate('timeasked').'</a><br>';
    
    if ($this->pubAsk and $this->fromuser->username !== User::DELETED_USER){
      echo 'From: '.$prUser('fromuser');}
    
    if ($extended) echo '<a class="date">Answered: ';
    else echo '<a class="date" href="question/'. $this->qid .'">Answered: ';
    
    echo $prDate('timeanswered').'</a></div><div class="ui ';
    if (! $partial) echo 'piled bottom ';
    echo 'attached segment"><div class="links"><a href="question/' .
      $this->qid .'/report"><i class="red flag link icon"></i></a>';
    
    if ($this->touser == $GLOBALS['user']) {
      echo '<br><a class="deleteq" href="question/' . $this->qid;
      echo '/delete"><i class="red trash link icon"></i></a>';
    }
    echo "</div><h3 class=\"ui header\">$this->question</h3><p>$this->answer</p></div>";
    
    if (! $partial) echo '</div>'; // else </div> will be closed in view
  }
  
  public function report($reason){
    
    if (! in_array($reason, array('illegal', 'threat', 'tos', 'porn', 'copyright', 'other')))
      throw new InvalidArgumentException("Select one of the listed reasons");
    
    $user = $GLOBALS['user'];
    
    if (! $this->touser->profileVisibleBy($user))
      throw new Exception('Sorry, you do not have the right to see this question');
    
    $query = "INSERT INTO question_reports (qid, reporter, reason)" .
        " VALUES ($this->qid, '$user', '$reason');";
    $res = $this->_db->query($query);
    if (! $res) 
      throw new RuntimeException($this->_db->error);
    
    return $res;
  }
  
  public function delete(){
    
    if ($GLOBALS['user']->username != $this->touser->username) 
      throw new Exception('You cannot delete this question');
    
    $del = $this->_db->query("DELETE FROM questions WHERE id = $this->qid;");
    if (!$del)
      throw new RuntimeException($this->_db->error);
    
    return $del;
  }
  
}
?>
