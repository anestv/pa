<?php namespace controllers;
use core\view as View;
use \Exception;

class Register extends \core\controller {
  
  public function get(){
    $this->requireUser('notloggedin');
    
    $data['title'] = 'Register';
    $data['styles'] = ['register.css'];
    
    View::rendertemplate('header', $data);
    View::render('register', $data);
    View::rendertemplate('footer', $data);
  }
  
  public function post(){
    $this->requireUser('notloggedin');
    
    if (!empty($_POST["datebirth"])){
      header("HTTP/1.1 418 I'm a teapot");
      session_write_close(); // let other requests continue
      sleep(70);
      die;
    }
    
    try {
      if (empty($_POST["ToS"]))
        throw new Exception('You must agree to the Terms and Conditions to use PrivateAsk');
      
      self::checkCaptcha();
      
      if (isset($_POST["username"]) and trim($_POST["username"]))
        $user = $_POST["username"];
      else throw new Exception("A username was not given");
      
      if (isset($_POST["password"]) and trim($_POST["password"]))
        $pass = $_POST["password"];
      else throw new Exception("A password was not given");
      
      if (isset($_POST["real"]) and trim($_POST["real"]))
        $realN = $_POST["real"];
      else throw new Exception("You did not enter your real name");
      
      $userObj = \models\User::create($user, $pass, $realN, $_POST['rand']);
      
      session_regenerate_id(true);
      $_SESSION['user'] = $user; // log him in
      
      // redirect to / where there will be a message
      // "Your account has been created and you are logged in"
      $_SESSION['registerSuccess'] = true;
      \helpers\Url::redirect('');
      
    } catch (Exception $e) {
      self::handleException($e);
      $this->get(); // print form
    }
  }
  
  public function postFb(){
    $this->requireUser('notloggedin');
    
    if (!($_SESSION['fbuser'] and $_SESSION['fbuser']['name'] and $_SESSION['fbuser']['id']))
      trigger_error("\$_SESSION['fbuser'] is not as expected");
    
    try {
      if (empty($_POST["ToS"]))
        throw new Exception('You must agree to the Terms and Conditions to use PrivateAsk');
      
      self::checkCaptcha();
      
      if (isset($_POST["username"]) and trim($_POST["username"]))
        $user = $_POST["username"];
      else throw new Exception("A username was not given");
      
      if (isset($_POST["password"]) and trim($_POST["password"]))
        $pass = $_POST["password"];
      else $pass = null;
      
      if (isset($_POST["real"]) and trim($_POST["real"]))
        $realN = $_POST["real"];
      else throw new Exception("You did not enter your real name");
      
      \models\FbUser::create($user, $_SESSION['fbuser']['id'], $realN, $_POST['rand'], $pass);
      
      session_regenerate_id(true);
      $_SESSION['user'] = $user; // log him in
      
      // redirect to / where there will be a message
      // "Your account has been created and you are logged in"
      $_SESSION['registerSuccess'] = true;
      unset($_SESSION['fbuser']);
      unset($_SESSION['requiredLogin']); // added at fblogin@facebookLogin
      
      \helpers\Url::redirect('');
      
    } catch (Exception $e) {
      $this->handleException($e);
      $this->getFb(); // print form
    }
  }
  
  public function getFb(){
    $this->requireUser('notloggedin');
    
    if (!($_SESSION['fbuser'] and $_SESSION['fbuser']['name'] and $_SESSION['fbuser']['id']))
      trigger_error("\$_SESSION['fbuser'] is not as expected");
    
    $data['title'] = 'Register';
    $data['styles'] = ['register.css'];
    
    $data['real'] = htmlspecialchars($_SESSION['fbuser']['name']);
    
    View::rendertemplate('header', $data);
    View::render('registerFb', $data);
    View::rendertemplate('footer', $data);
  }
  
  private static function checkCaptcha(){
    if (! ENABLE_CAPTCHA) return;
    
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=". RECAPTCHA_SECRET;
    $url.= "&response=".$_POST['g-recaptcha-response'];
    
    $captcha = json_decode(file_get_contents($url));
    
    if ($captcha and !$captcha->success)
      throw new Exception("You did not pass the captcha");
  }
}
