<?php namespace models;
use \Exception, \RuntimeException;

class FbUser extends User {
  
  public $fbid;
  
  public function __construct($fbid){
    
    $fbid = self::$_db->real_escape_string($fbid);
    $query = "SELECT * FROM users WHERE fbid = '$fbid';";
    
    $res = self::$_db->query($query);
    
    if (! $res) throw new RuntimeException(self::$_db->error);
    if ($res->num_rows < 1)
      throw new Exception('No user with such id found', 404);
    
    parent::__construct($res->fetch_assoc()['username']); // how can we not query the db again?
    $this->fbid = $fbid;
  }
  
  public static function create($username, $fbid, $realname, $rand, $password = null){
    
    // validate user pass real (regexps and length)
    if (preg_match('/^\w{5,20}$/', $username) !== 1)
      throw new Exception('Enter 5-20 English letters and numbers as username');
    if (strlen($realname) > 40)
      throw new Exception('Enter a real name up to 40 characters');
    $realname = self::$_db->real_escape_string($realname);
    
    // case-insensitive check, even though check for deleteduser in Question.writedown is case sensitive
    $regex = '/^'.self::ANONYMOUS.'|'.self::DELETED_USER.'$/i'; 
    if (preg_match($regex, $username) === 1)
      throw new Exception("Do not use '$username' as a username, as it has a special meaning for the server");
    
    // is there a user with the same username or fbid?
    try {
      $testUser = new parent($username);
    } catch (Exception $e) {
      $notExist = $e->getCode() == 404;
    }
    if (!$notExist and $testUser->isRealUser())
      throw new Exception("A user named $username already exists");
    
    try {
      $testUser = new self($fbid);
    } catch (Exception $e) {
      $notExist = $e->getCode() == 404;
    }
    if (!$notExist)
      throw new Exception("A user with the same fbid exists");
    
    
    if ($password){
      if (strlen($password) < 6)
        throw new Exception('Please enter a password of more than 6 characters');
      if (strlen($password) > 100)
        throw new Exception('Please enter a password up to 100 characters');
      
      // hash and salt teh password
      $hexrand = bin2hex(openssl_random_pseudo_bytes(10));
      $thirand = base_convert($hexrand, 16, 30);
      $alataki = $thirand. $rand. $thirand; // in case $rand is too small
      $cr_arr = ['salt'=> $alataki, 'cost'=> 10];
      $hspass = password_hash($pass, PASSWORD_DEFAULT, $cr_arr);
      $passDB = self::$_db->real_escape_string($hspass);
      
    } else $passDB = '-';
    
    $query = "INSERT INTO users(username, fbid, hs_pass, realname) ".
      "VALUES ('$username', '$fbid', '$passDB', '$realname');";
    $result = self::$_db->query($query);
    
    if (!$result) throw new RuntimeException(self::$_db->error);
    
    return new self($fbid);
  }
  
  public static function addFbLogin($user, $fbid){
    if ($user instanceof User)
      $user = $user->username;
    
    try {
      $testUser = new self($fbid);
    } catch (Exception $e) {
      $notExist = $e->getCode() == 404;
    }
    if (!$notExist)
      throw new Exception("A user with the same fbid exists");
    
    $user = self::$_db->real_escape_string($user);
    $fbid = self::$_db->real_escape_string($fbid);
    
    $query = "UPDATE users SET fbid = '$fbid' WHERE username = '$user';";
    $res = self::$_db->query($query);
    
    if (!$res) throw new RuntimeException(self::$_db->error);
  }
  
  public static function removeFbLogin($user){
    if ($user instanceof User)
      $user = $user->username;
    
    $user = self::$_db->real_escape_string($user);
    
    $query = "UPDATE users SET fbid = NULL WHERE username = '$user';";
    $res = self::$_db->query($query);
    
    if (!$res) throw new RuntimeException(self::$_db->error);
  }
}
