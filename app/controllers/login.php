<?php namespace controllers;
use core\view as View;
use \Exception;

class Login extends \core\controller{
  
  public function __construct(){
    parent::__construct();
  }
  
  public function get(){	
    
    $data['title'] = 'Login';
    
    View::rendertemplate('header',$data);
    View::render('login',$data);
    View::rendertemplate('footer',$data);
  }
  
  public function post(){
    try {
    if (isset($_POST["user"]) and trim($_POST["user"]))
      $user = $_POST["user"];
    else throw new Exception('You did not enter a username');
    
    if (isset($_POST['pass']) and trim($_POST['pass']))
      $pass = $_POST['pass'];
    else throw new Exception('You did not enter a password');
    
    $user = new \models\user($user);
    
    if (!$user->checkPassword($pass))
      throw new Exception('The password you entered is incorrect');
    
    $user->preventDeleteion();
    
    if (!empty($_POST["keep"]))
      session_set_cookie_params(60*60*24*7); //1 evdomada
    session_regenerate_id(true);
    $_SESSION['user'] = $user_db['username']; //proper case (capitals or small)
    
    if (isset($_SESSION['requiredLogin']))
      redirect($_SESSION['requiredLogin']);
    else redirect(''); //redirect to /pa/
    
    } catch (Exception $e){
      self::handleException($e); //warning message
      $this->get(); //show login form
    }
  }
	
}