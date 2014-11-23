<?php namespace models;
use \Exception, \RuntimeException;

class User extends \core\model {
  
  const NOT_LOGGED_IN = 'none';
  const DELETED_USER = 'deleteduser';
  const ANONYMOUS = 'anonymous';
  const CURRENT = 'curr';
  
  public $username, $hs_pass, $realname, $whosees, $whoasks, $deactivated, $style, $raw;
  
  
  // find an existing user by username
  public function __construct($username){
    parent::__construct();
    
    if ($username == self::CURRENT){
      if ($_SESSION['user'])
        $username = $_SESSION['user'];
      else $username = self::NOT_LOGGED_IN;
    }
    
    switch ($username){
      case self::NOT_LOGGED_IN:
      case self::DELETED_USER:
      case self::ANONYMOUS:
        $this->username = $username;
        return;
    }
    
    $username = $this->_db->real_escape_string($username);
    $res = $this->_db->query("SELECT * FROM users WHERE username = '$username';");
    
    if (!$res) throw new RuntimeException($this->_db->error);
    if ($res->num_rows < 1){
      $this->username = self::NOT_LOGGED_IN;
      throw new Exception("No user named $username was found", 404);
      return;
    }
    
    $user = $this->raw = $res->fetch_assoc();
    
    $this->username = $user['username'];
    $this->realname = $user['realname'];
    $this->hs_pass = $user['hs_pass'];
    $this->whosees = $user['whosees'];
    $this->whoasks = $user['whoasks'];
    $this->deactivated = $user['deleteon'] !== null;
    $this->style = array_intersect_key($user,
        ['headcolor'=>0, 'backcolor'=>0, 'textfont'=>0]); // get the common keys
  }
  
  
  public static function create($username, $password, $realname, $rand){
    
    // validate user pass real (regexps and length)
    if (preg_match('/^\w{5,20}$/', $username) !== 1)
      throw new Exception('Enter 5-20 English letters and numbers as username');
    if (strlen($password) < 6)
      throw new Exception('Please enter a password of more than 6 characters');
    if (strlen($password) > 100)
      throw new Exception('Please enter a password up to 100 characters');
    if (strlen($realname) > 40)
      throw new Exception('Enter a real name up to 40 characters');
    
    // no need to escape user (passed regex) or pass (will be hashed)
    
    $realname = self::$_db->real_escape_string($realname);
    
    // case-insensitive check, even though check for deleteduser in Question.writedown is case sensitive
    $regex = '/^'.self::ANONYMOUS.'|'.self::DELETED_USER.'$/i'; 
    if (preg_match($regex, $username) === 1)
      throw new Exception("Do not use '$username' as a username, as it has a special meaning for the server");
    
    // is there a user with the same username?
    try {
      $testUser = new self($username);
    } catch (Exception $e) {
      $notExist = $e->getCode() == 404;
    }
    if (!$notExist and $testUser->isRealUser())
      throw new Exception("A user named $username already exists");
    
    // hash and salt teh password
    $hexrand = bin2hex(openssl_random_pseudo_bytes(10));
    $thirand = base_convert($hexrand, 16, 30);
    $alataki = $thirand. $rand. $thirand;
    $cr_arr = array('salt'=> $alataki, 'cost'=> 10);
    $hspass = password_hash($pass, PASSWORD_DEFAULT, $cr_arr);
    $passDB = self::$_db->real_escape_string($hspass);
    
    // insert data into database
    $query = "INSERT INTO users(username, hs_pass, realname) VALUES ('$username', '$passDB', '$realname');";
    $result = self::$_db->query($query);
    
    if (!$result) throw new RuntimeException(self::$_db->error);
    
    return new self($username);
  }
  
  public function checkPassword($password){
    return password_verify($password, $this->hs_pass);
  }
  
  public function changePassword($newPass){ //must have arleady verified newpass == newpass2
    // hash password and put it in $this->hs_pass
    // update db entry
  }
  
  public function isRealUser(){
    
    switch ($this->username){
      
      case self::NOT_LOGGED_IN:
      case self::DELETED_USER:
      case self::ANONYMOUS:
        return false;
      
      default:
        return true;
    }
  }
  
  function hasFriend($user){ //could be protected
    if ($user instanceof User)
      $user = $user->username;
    else $user = $this->_db->real_escape_string($user);
    
    $query = "SELECT friend FROM friends WHERE `user`='$this->username' AND friend='$user';";
    $res = $this->_db->query($query);
    
    return (($user === $this->username) or ($res and $res->num_rows > 0));
  }
  
  public function profileVisibleBy($user){
    if ($user instanceof User)
      $user = $user->username;
    
    if ($user === self::DELETED_USER or $user === self::ANONYMOUS)
      throw new Exception('Invalid user provided'); // too general?
    
    if ($user === self::NOT_LOGGED_IN)
      return $this->whosees === 'all';
    
    if ($this->whosees === 'users')
      return true;
    
    return $this->hasFriend($user);
  }
  
  public function askableBy($user){
    if ($user instanceof User)
      $user = $user->username;
    
    if ($user === self::DELETED_USER or $user === self::ANONYMOUS)
      throw new Exception('Invalid user provided'); // too general?
    
    if ($user === self::NOT_LOGGED_IN)
      return $this->whoasks === 'all';
    
    if ($this->whoasks === 'users')
      return true;
    
    return $this->hasFriend($user);
  }
  
  public function getUnseen(){
    $user = $this->username;
    $query = "SELECT COUNT(*) FROM questions WHERE touser = '$user' AND answer IS NULL;";
    $res = $this->_db->query($query);
    
    if (!$res) throw new RuntimeException($this->_db->error);
    
    $unseen = intval($res->fetch_array()[0]);
    if ($unseen > 99) $unseen = '99+';
    
    return $unseen;
  }
  
  public function editSettings($s){
    $privacyVals = ['friends', 'users', 'all'];
    $fonts = ["Aliquam","Arial","Calibri","Cambria","Comfortaa","Comic Sans MS","Courier","Garamond","Josefin Sans","Leander","Segoe UI","Tahoma","Times New Roman","Trench","Verdana"];
    $warn = '';
    
    if (!is_array($s))
      throw new Exception('Argument is not an array');
    
    $s = array_intersect_key($s, $this->raw); // keep only keys present in $this->raw
    
    if (!in_array($s['whosees'], $privacyVals)){
      $warn .= '<li>Choose one of the shown privacy settings';
      unset($s['whosees']);
    }
    
    if (!in_array($s['whoasks'], $privacyVals)){
      $warn .= '<li>Choose one of the shown privacy settings';
      unset($s['whoasks']);
    }
    
    if (isset($s['realname'])) {
      if (trim($s['realname'])) {
        $realtmp = htmlspecialchars(substr($s['realname'], 0, 40)); // first 40 chars
        $s['realname'] = $this->_db->real_escape_string($realtmp);
      } else {
        $warn .= '<li>Enter your real name';
        unset($s['realname']);
      }
    }
    
    if (isset($s['textfont']) and !in_array($s['textfont'], $fonts)){
      $warn .= '<li>Select a font family';
      unset($s['textfont']);
    }
    
    if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $s['backcolor']) !== 1){
      $warn .= '<li>Select a background color';
      unset($s['backcolor']);
    }
    
    if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $s['headcolor']) !== 1){
      $warn .= '<li>Select a header color';
      unset($s['headcolor']);
    }
    
    // if a setting doesn't exist or is not valid, use its current value
    $this->raw = array_merge($this->raw, $s);
    
    // don't update username, hs_pass and deleteon
    $toUpdate = array_diff_key($this->raw, ['username' => 1, 'deleteon' => 1, 'hs_pass' => 1]);
    
    $set = '';
    foreach($toUpdate as $k=>$v)
      $set .= "$k = '$v', ";
    $set = rtrim($set, ', ');
    
    $query = "UPDATE users SET $set WHERE username = '$this->username';";
    
    $res = $this->_db->query($query);
    
    if (! $res)
      $warn .= '<li>Your settings were not changed due to a server error.'.$this->_db->error;
    
    return ($warn ?: false);
  }
  
  public function editFriends($action, $argument){
    if ($action == 'add')
      Friend::addFriend($this->username, $argument);
    else if ($action == 'remove')
      Friend::removeFriend($this->username, $argument);
    else if ($action == 'set')
      Friend::setFriends($this->username, $argument);
    else
      throw new Exception('The provided action is not supported', 400);
  }
  
  public function deleteAccount($username, $password){
    if ($username !== $this->username)
      throw new Exception("You did not enter your account's username");
    
    if (!$this->checkPassword($password))
      throw new Exception('The password you entered is incorrect');
    
    $query = "UPDATE users SET deleteon = CURRENT_DATE + INTERVAL 7 DAY WHERE username = '$username';";
    $res = $this->_db->query($query);
    
    if (!$res) throw new RuntimeException($this->_db->error);
  }
  
  public function preventDeletion(){
    $this->_db->query("UPDATE users SET deleteon = NULL WHERE username = '$this->username';");
    return $this->_db->affected_rows > 0; // if anything changed
  }
  
  public function __toString(){
    return $this->username;
  }
  
}
 
?>
