<?php namespace models;
 
class Friend extends \core\model {
  
  public static function addFriend($user, $friend){
    $res = $this->_db->query("INSERT INTO friends VALUES ('$user', '$friend');");
    
    if ($this->_db->errno == 1062)
      throw new Exception("You are already friends with $friend");
    
    return true;
  }
  
  public static function removeFriend($user, $friend){
    $res = $this->_db->query("DELTETE FROM friends WHERE `user`='$user' AND friend='$friend';");
    
    if ($res and $con->affected_rows === 0)
      throw new Exception("$friend was not in your friends anyway");
    
    if ($this->_db->errno == 1452)
      throw new Exception("It looks like $friend is not yet registered");
    
    return true;
  }
  
  public static function setFriends($user, $friendList){
    $friends = json_decode($friendList);
    if (! ($friends and is_array($friends)))
      throw new InvalidArgumentException("Your friends were not provided as a correct JSON");
    
    $friends = array_filter($friends, "is_string"); //remove non-string elements
    
    $friends = array_unique($friends);
    
    //does he have himself as friend?
    $userinfriends = array_search($user, $friends);
    if ($userinfriends !== false)
      array_splice($friends, $userinfriends, 1);
    
    //clear previous friends
    $res = $this->_db->query("DELETE FROM friends WHERE `user` = '$user';");
    if (!$res) throw new RuntimeException("MySQL error ".$this->_db->error);
    
    $stmt = $this->_db->prepare("INSERT INTO friends VALUES ('$user', ?);");
    
    $stmt->bind_param('s', $curr);
    
    foreach ($friends as $curr)
      $stmt->execute(); //$curr is registered and we dont have to bind it every time
    
    $stmt->close();
    return true;
  }
  
  public function __construct(){
    parent::__construct();
  }
  
}
?>
