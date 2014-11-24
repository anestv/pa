<?php namespace controllers;
use core\view as View;
use \Exception;
use \models\User as User;

class Profile extends \core\controller{
  
  public function profile($username){
    try {
      $user = new User($username);
      $username = $user->username;
      
      if (! $user->isRealUser())
        throw new Exception('This user does not seem to exist.', 404);
      
      if ($user->deactivated)
        throw new Exception('This user has deactivated their account.');
      
      $data['askable'] = $user->askableBy($GLOBALS['user']);
      $data['visible'] = $user->profileVisibleBy($GLOBALS['user']);
      
      if (!$GLOBALS['user']->isRealUser() or $GLOBALS['user']->username == $username)
        $friendButton = 'none';
      else if ($GLOBALS['user']->hasFriend($user))
        $friendButton = 'removeFrom';
      else
        $friendButton = 'addTo';
    } catch (Exception $e) {
      $this->handleException($e);
    }
    
    $data['title'] = $username;
    $data['styles'] = ['profile.css', "/api/profileDisplay/$username"];
    $data['bodyData'] = 'data-owner="' . $username . '"';
    $data['scripts'] = ['jquery' => 1, 'semantic' => 1, 'jquery.age' => 1];
    $data['scripts']['custom'] = ['profile.js'];
    
    $data['owner'] = $user;
    $data['friendBut'] = $friendButton;
    $data['loggedin'] = $GLOBALS['user']->isRealUser();
    
    
    View::rendertemplate('header', $data);
    View::render('profile', $data);
    View::rendertemplate('footer', $data);
  }
  
}
?>
