<?php namespace controllers;
use core\view as View;
use \Exception;
use \models\User as User;

class Profile extends \core\controller{
  
  const UNABLE = 0, ABLE = 1, TRY_LOGIN = 2;
  const FRIEND_NO_BUTTON = 0, FRIEND_ADD_BUTTON = 1, FRIEND_REMOVE_BUTTON = 2;
  const PUBASK_NEVER = 0, PUBASK_CHOOSE = 1, PUBASK_ALWAYS = 2;
  
  public function profile($username){
    try {
      $user = new User($username);
      $username = $user->username;
      
      if (! $user->isRealUser())
        throw new Exception('This user does not seem to exist.', 404);
      
      if ($user->deactivated)
        throw new Exception('This user has deactivated their account.');
      
      try {
        $questions = \models\LoadQ::main($user, 0);
      } catch (Exception $e) {}
      
    } catch (Exception $e) {
      self::handleException($e);
      self::errorMessage($e->getMessage());
      // execution is stopped, no need to return
    }
    
    $loggedin = $GLOBALS['user']->isRealUser();
    
    $data = [
      'title' => $username,
      'styles' => [
        'profile.css',
        "/api/profileDisplay/$username"
      ],
      'bodyData' => "data-owner='$username'",
      'scripts' => [
        'jquery' => 1,
        'semantic' => 1,
        'jquery.age' => 1,
        'custom' => ['profile.js']
      ],
      'owner' => $user,
      'questions' => $questions
    ];
    
    
    if (!$loggedin or $GLOBALS['user']->username == $username)
      $data['friendBut'] = self::FRIEND_NO_BUTTON;
    else if ($GLOBALS['user']->hasFriend($user))
      $data['friendBut'] = self::FRIEND_REMOVE_BUTTON;
    else
      $data['friendBut'] = self::FRIEND_ADD_BUTTON;
    
    if ($user->askableBy($GLOBALS['user']))
      $data['ask'] = self::ABLE;
    else
      $data['ask'] = $loggedin ? self::UNABLE : self::TRY_LOGIN;
    
    if ($user->profileVisibleBy($GLOBALS['user']))
      $data['see'] = self::ABLE;
    else
      $data['see'] = $loggedin ? self::UNABLE : self::TRY_LOGIN;
    
    if (!$loggedin)
      $data['pubask'] = self::PUBASK_NEVER;
    else if ($GLOBALS['user']->username == $username)
      $data['pubask'] = self::PUBASK_ALWAYS;
    else
      $data['pubask'] = self::PUBASK_CHOOSE;
    
    
    View::rendertemplate('header', $data);
    View::render('profile', $data);
    View::rendertemplate('footer', $data);
  }
  
}
?>
