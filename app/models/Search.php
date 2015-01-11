<?php namespace models;
use \Exception, \RuntimeException, \InvalidArgumentException;

class Search extends \core\model {
  
  //check if at least one search query exist
  public static function searchQueriesExist(){
    
    if (empty($_GET['lookfor']))
      return false;
    
    if ($_GET['lookfor'] === 'qa')
      $arr = ['query', 'fromuser', 'touser', 'timeanswered'];
    else $arr = ['username', 'realname'];
    
    foreach($arr as $curr)
      if (isset($_GET[$curr]) and trim($_GET[$curr]))
        return true;
    
    return false;
  }
  
  public static function userSearch(){
    
    $escape = function($str){
      $a = preg_replace('/(_|%|\|)/', '|\1', $str);
      return self::$_db->real_escape_string($a);
    };
    
    $username = $_GET['username'];
    $realname = $_GET['realname'];
    
    if ($username and strlen($username) < 3)
      throw new InvalidArgumentException('Enter at least 3 characters at "Username"');
    if ($realname and strlen($realname) < 3)
      throw new InvalidArgumentException('Enter at least 3 characters at "Real Name"');
    
    $query = "SELECT username, realname FROM users WHERE username LIKE '".
        $escape($username)."%' AND realname LIKE '".$escape($realname)."%';";
    $res = self::$_db->query($query);
    if (!$res) throw new RuntimeException(self::$_db->error);
    
    return $res;
  }
  
  public static function QASearch(){
    $user = $GLOBALS['user']->isRealUser() ? $GLOBALS['user']->username : '';
    
    $query = "SELECT DISTINCT questions.* FROM questions, users, friends WHERE answer IS NOT NULL";
    
    if (!empty($_GET['query'])){
      if (strlen(trim($_GET['query'])) < 5)
        throw new InvalidArgumentException('Please enter at least five characters as a query');
      
      $escapedQuery = self::$_db->real_escape_string($_GET['query']);
      $query .= " AND MATCH(question, answer) AGAINST ('$escapedQuery')";
    }
    
    if (empty($_GET['fromuser']))
      $fromuser = '';
    else {
      $tmpfrom = new User($_GET['fromuser']);
      $fromuser = $tmpfrom->isRealUser() ? $tmpfrom->username : '-';
      // - is not a valid username, so no results will return
    }
    
    if (empty($_GET['touser']))
      $touser = '';
    else {
      $tmpto = new User($_GET['touser']);
      $touser = $tmpto->isRealUser() ? $tmpto->username : '-';
      // - is not a valid username, so no results will return
    }
    
    if ($_GET['timeanswered']){
      if (preg_match("/^(1|2)\\d{3}-(0[1-9]|10|11|12)$/", $_GET['timeanswered']) === 1){
        
        $date = $_GET['timeanswered'] . '-01';
        
        if (new DateTime($date) > new DateTime()) // if it is a future date
          throw new Exception('Enter a month in the past');
        
        $query .= " AND timeanswered BETWEEN '$date' AND '$date' + INTERVAL 1 MONTH - INTERVAL 1 DAY";
      } else throw new InvalidArgumentException('Enter the month in the format yyyy-mm');
    }
    
    if ($_GET['sort']){
      switch($_GET['sort']){
        case 'userasc' : $sortq = 'ORDER BY touser'; break;
        case 'userdesc': $sortq = 'ORDER BY touser DESC'; break;
        case 'timeasc' : $sortq = 'ORDER BY timeanswered'; break;
        case 'timedesc': $sortq = 'ORDER BY timeanswered DESC'; break;
        default :
          throw new InvalidArgumentException('Choose one of the listed sorting criteria');
      }
    } else $sortq = '';
    
    if ($fromuser) $query .= " AND fromuser = '$fromuser'";
    if ($touser) $query .= " AND touser = '$touser'";
    
    if ($fromuser and $fromuser !== $user)
      $query .= ' AND publicasker = 1';
    
    $query .= " AND questions.touser = users.username AND deleteon IS NULL AND (";
    if ($user){ //takes care of privacy
      $query .= "whosees = 'users' OR username = '$user' OR ";
      $query .= "(`friends`.`user` = username AND friends.friend = '$user') OR ";
    }
    $query .= "whosees = 'all') $sortq LIMIT 50;"; //maybe do something about the limit in the future
    
    $qs = new QuestionSet($query);
    
    return $qs->members;
  }
}
