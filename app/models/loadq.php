<?php namespace models;
use \Exception, \RuntimeException;

class LoadQ extends \core\model {
  
  public static function main($owner, $offset = 0){
    if (!is_numeric($offset) or $offset < 0)
      throw new InvalidArgumentException('The offset is not of correct type.');
    $offset = intval($offset);
    
    try {
      if (!($owner instanceof User))
        $owner = new User($owner);
      
      if ($owner->deactivated)
        throw new Exception('This user has deactivated their account.');
      
      if (! $owner->profileVisibleBy($GLOBALS['user']))
        throw new Exception("You are not allowed to view this user's questions", 403);
      
      $query = "SELECT id FROM questions WHERE answer IS NOT NULL AND touser".
        " = '$owner->username' ORDER BY timeanswered DESC LIMIT 11 OFFSET $offset;";
      $res = self::$_db->query($query);
      if (!$res)
        throw new RuntimeException(self::$_db->error);
      
    } catch (Exception $e) {
      throw $e;
      // TODO anything better?
    }
    
    if ($res->num_rows === 0)
      http_response_code(204); //No content
    else {
      
      $i = 1;
      while(($row = $res->fetch_array()) and $i++ !== 11){
        $q = new Question($row['id']);
        
        $q->writeOut();
      }
      
      return $res;
    }
  }
}
?>
