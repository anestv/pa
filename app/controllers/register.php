<?php namespace controllers;
use core\view as View;
use \Exception;

class Register extends \core\controller {
  
  public function get(){
    $this->requireUser('notloggedin');
    
    $data['title'] = 'Register';
    $data['styles'][] = 'register.css';
    
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
      
      if (isset($_POST["username"]) and trim($_POST["username"]))
        $user = $_POST["username"];
      else throw new Exception("A username was not given");
      
      if (isset($_POST["password"]) and trim($_POST["password"]))
        $pass = $_POST["password"];
      else throw new Exception("A password was not given");
      
      if (isset($_POST["real"]) and trim($_POST["real"]))
        $realN = $_POST["real"];
      else throw new Exception("You did not enter your real name");
      
      $this->user = \models\User::create($user, $pass, $realN, $_POST['rand']);
      
      redirect("user/$user", 201); //won't redirect, just set Location, http created
      session_regenerate_id(true);
      $_SESSION['user'] = $user; // log him in
      
      // TODO redirect to / where there will be a message
      // "Your account has been created and you are logged in"
      
    } catch (Exception $e) {
      $this->handleException($e);
      $this->get(); // print form
    }
  }
  
}
