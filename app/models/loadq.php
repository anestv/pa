<?php namespace models;
use \Exception;

class LoadQ extends \core\model {
  
  public static function main($owner, $offset = 0){
    if (!is_numeric($offset) or $offset < 0)
      throw new \InvalidArgumentException('The offset is not of correct type.');
    $offset = intval($offset);
    
    
    if (!($owner instanceof User))
      $owner = new User($owner);
    
    if ($owner->deactivated)
      throw new Exception('This user has deactivated their account.');
    
    if (! $owner->profileVisibleBy($GLOBALS['user']))
      throw new Exception("You are not allowed to view this user's questions", 403);
    
    $query = "SELECT * FROM questions WHERE answer IS NOT NULL AND touser".
      " = '$owner->username' ORDER BY timeanswered DESC LIMIT 11 OFFSET $offset;";
    
    $qset = new QuestionSet($query);
    
    $count = count($qset->members);
    
    return $qset->members; // array of \model\Questions
  }
}
?>
