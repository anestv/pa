<?php namespace controllers;
use core\view as View;
use \Exception;

class Login extends \core\controller{
  
  public function __construct(){
    parent::__construct();
  }
  
  public function get(){
    $this->requireUser('notloggedin');
    
    $data['title'] = 'Login';
    
    $data['loggedOut'] = $_SESSION['loggedOut'];
    $_SESSION['loggedOut'] = false;
    
    View::rendertemplate('header',$data);
    View::render('login',$data);
    View::rendertemplate('footer',$data);
  }
  
  public function post(){
    $this->requireUser('notloggedin');
    
    try {
    if (isset($_POST["user"]) and trim($_POST["user"]))
      $user = $_POST["user"];
    else throw new Exception('You did not enter a username');
    
    if (isset($_POST['pass']) and trim($_POST['pass']))
      $pass = $_POST['pass'];
    else throw new Exception('You did not enter a password');
    
    $this->user = new \models\user($user);
    
    if (!$this->user->isRealUser())
      throw new Exception('This user doesn\'t exist. Do you want to <a href="register">register</a>?');
    
    if (!$this->user->checkPassword($pass))
      throw new Exception('The password you entered is incorrect');
    
    $this->user->preventDeletion();
    
    if (!empty($_POST["keep"]))
      session_set_cookie_params(60*60*24*7); //1 evdomada
    session_regenerate_id(true);
    $_SESSION['user'] = $this->user->username; //proper case (capitals or small)
    
    if (isset($_SESSION['requiredLogin']))
      \helpers\Url::redirect($_SESSION['requiredLogin']);
    else \helpers\Url::redirect(''); //redirect to /pa/
    
    } catch (Exception $e){
      self::handleException($e); //warning message
      $this->get(); //show login form
    }
  }
  
  public function logout(){
    $_SESSION['user'] = '';
    $_SESSION['loggedOut'] = true;
    \helpers\Url::redirect('login');
  }
}