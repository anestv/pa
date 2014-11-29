<?php namespace models;

use \Exception, \RuntimeException, \InvalidArgumentException;

class Question extends \core\model {
  
  public $qid, $question, $answer, $fromuser, $touser;
  public $timeasked, $timeanswered, $pubAsk;
  
//  protected static $stmtInit = self::$_db->prepare("SELECT * FROM questions WHERE id = ?;");
  
  public function __construct($qid = null){
    parent::__construct();
    
    if (null === $qid) return;
    
    if (!is_numeric($qid) or $qid <= 0)
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
    
    if (!$touser->isRealUser())
      throw new Exception('This user does not seem to exist');
    
    if ($touser->deactivated)
      throw new Exception('This user has deactivated their account');
    
    // ensure proper case (because == is case sensitive)
    $to = $touser->username;
    $from = $from->username;
    
    if (!$touser->askableBy($from))
      throw new Exception('Sorry, you cannot ask this user a question', 403);
    
    $pubAsk = ($pubAsk ? 1 : 0);
    
    if ($from == User::NOT_LOGGED_IN) {
      $from = User::ANONYMOUS;
      $pubAsk = 0;
    } else if ($from == $to)
      $pubAsk = 1;
    
    $question = self::$_db->real_escape_string(htmlspecialchars($text));
    
    $query = "INSERT INTO questions (fromuser, touser, question, publicasker)".
      " VALUES ('$from', '$to', '$question', $pubAsk);";
    
    $res = self::$_db->query($query);
    if (! $res) throw new RuntimeException(self::$_db->error);
    
    return new self(self::$_db->insert_id);
  }
  
  public function answer($text){
    if ($GLOBALS['user']->username != $this->touser->username) 
      throw new Exception('You cannot answer this question', 403);
    
    if (! empty($this->answer))
      throw new Exception("You have already answered this question; you can't reanswer it", 405);
    
    $this->answer = htmlspecialchars($text);
    $answer = $this->_db->real_escape_string($this->answer);
    
    $query = "UPDATE questions SET answer = '$answer',timeanswered = NOW() WHERE id = $this->qid;";
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
    
    echo '<div class="question">';
    
    // HEADER:
    
    echo '<div class="ui top attached tiny header"><table>';
    
    if ($extended){
      echo '<tr><td>To: '.$prUser('touser').'</td><td>';
      echo '<a class="date">Asked: '.$prDate('timeasked').'</a></td></tr>';
    }
    
    echo '<tr><td>';
    $showFrom = $this->pubAsk and $this->fromuser->username !== User::DELETED_USER;
    if ($showFrom) echo 'From: '.$prUser('fromuser');
    
    echo '</td><td>';
    
    if ($extended){
      if ($this->answer)
        echo '<a class="date">Answered: '.$prDate('timeanswered').'</a>';
    } else {
      echo '<a class="date" href="question/'.$this->qid.'">';
      if ($this->answer)
        echo 'Answered: '.$prDate('timeanswered').'</a>';
      else
        echo 'Asked: '.$prDate('timeasked').'</a>';
    }
    echo '</td></tr></table></div>';
  
    // MAIN BODY: 
    echo '<div class="ui ';
    if (! $partial) echo 'piled bottom ';
    echo 'attached segment"><div class="links"><a href="question/'. $this->qid;
    
    if (empty($this->answer) and $this->touser->username == $GLOBALS['user']->username)
      echo '/answer"><i class="black pencil link icon"></i></a>';
    else
      echo '/report"><i class="red flag link icon"></i></a>';
    
    if ($this->touser->username == $GLOBALS['user']->username) {
      echo '<br><a class="deleteq" href="question/' . $this->qid;
      echo '/delete"><i class="red trash link icon"></i></a>';
    }
    echo "</div><h3 class=\"ui header\">$this->question</h3>";
    if ($this->answer) echo "<p>$this->answer</p>";
    echo '</div>';
    
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
