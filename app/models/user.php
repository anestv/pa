<?php namespace models;
 
class User extends \core\model {
  
  const NOT_LOGGED_IN = NULL;
  const DELETED_USER = 'deleteduser';
  const ANONYMOUS = 'anonymous';
  
  public $username, $hs_pass, $realname, $whosees, $whoasks, $deactivated;
  
  
  // find an existing user by username
  public function __construct($username){
    parent::__construct();
    
    if ($username == self::NOT_LOGGED_IN)
      ;// do sth
    else if ($username == self::DELETED_USER)
      ;// do sth
    else if ($username == self::ANONYMOUS)
      ;// do sth
    
    // search DB for this username and if found
    // make $this that user
    // if deleteon != null, make this user DELETED_USER
  }
  
  
  public static function create($username, $password, $realname, $salt){
    // validate user pass real (regexps and length)
    
    // no need to check case-insensitive (check for deleteduser in Question.writedown is case sensitive)
    if ($username === self::ANONYMOUS or $username === self::DELETED_USER)
      throw new Exception("Do not use '$username' as a username, as it has a special meaning for the server");
    
    // hash password
    // insert data into database
    return new self($username, $passowrd);
  }
  
  public function checkPassword($password){
    return password_verify($password, $this->hs_pass);
  }
  
  public function changePassword($newPass){ //must have arleady verified newpass == newpass2
    // hash password and put it in $this->hs_pass
    // update db entry
  }
  
  function hasFriend($friend){ //could be protected
    if ($user instanceof User)
      $user = $user->username;
    
    
  }
  
  public function profileVisibleBy($user){
    if ($user instanceof User)
      $user = $user->username;
    
    if ($user === self::NOT_LOGGED_IN)
      return $this->whosees === 'all';
    
    if ($this->whosees === 'users')
      return true;
    
    return $this->hasFriend($user);
  }
  
  public function askableBy($user){
    if ($user instanceof User)
      $user = $user->username;
    
    if ($user === self::NOT_LOGGED_IN)
      return $this->whoasks === 'all';
    
    if ($this->whoasks === 'users')
      return true;
    
    return $this->hasFriend($user);
  }
  
  
  public function editFriends($action, $argument){
    if ($action == 'add')
      Friend::addFriend($this->username, $argument);
    else if ($action == 'remove')
      Friend::removeFriend($this->username, $argument);
    else if ($action == 'set')
      Friend::setFriends($this->username, $argument);
    else
      throw new Exception(); //or something similar
  }
  
  public function deleteAccount($username, $password){
    if ($username !== $this->username)
      throw new Exception("You did not enter your account's username");
    
    if (!$this->checkPassword($password))
      throw new Exception('The password you entered is incorrect');
    
    $query = "UPDATE users SET deleteon = CURRENT_DATE + INTERVAL 7 DAY WHERE username = '$username';";
    $res = $con->query($query);
    
    if (!$res) throw new RuntimeException($con->error);
  }
  
  public function preventDeletion(){
    $con->query("UPDATE users SET deleteon = NULL WHERE username = '$user';");
    return $con->affected_rows > 0; // if anything changed
  }
  
}
 
?>
