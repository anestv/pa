<?php namespace controllers;
use core\view as View;
use \Exception;

class FbLogin extends \core\controller{
  
  public function facebooklogin(){
    $this->requireUser('notloggedin');
    
    try {
      $fb = \helpers\MyFB::$facebook;
      
      $sess = $fb->getSessionfromRedirect();
      
      if (!$sess)
        throw new Exception('Make sure you allow PrivateAsk to access your basic profile info');
      
      $fbuser = \helpers\MyFB::getStuff($sess->getToken());
      
      try {
        $user = new \models\FbUser(strval($fbuser['id']));
        
        if (!$user->isRealUser())
          throw new Exception('Unexcepted user');
        
      } catch (Exception $ex) { // if account doesnt exist, register
        
        if ($ex->getCode() != 404) throw $ex;
        // if it is something other than user not found (not
        // registered) deal with it in the outer catch
        
        $_SESSION['fbuser'] = $fbuser;
        $_SESSION['requiredLogin'] = 'api/connectFb';
        // redirect there after log in, variable is unset in c\register@postFb
        
        $data = [];
        
        View::rendertemplate('header', $data);
        View::render('preregisterFb', $data);
        View::rendertemplate('footer', $data);
        return;
      }
      
      session_regenerate_id(true);
      $_SESSION['user'] = $user->username;
      
      \helpers\Url::redirect('');
      
    /*} catch (Facebook\FacebookRequestException $e) {
      self::handleException($e);
      //TODO print a message*/
    } catch (Exception $e) {
      self::handleException($e);
    }
  }
  
  public function connectFb(){
    $this->requireUser('loggedin');
    
    $fb = \helpers\MyFB::setPath('api/connectFb');
    
    $sess = $fb->getSessionfromRedirect();
    
    if ($sess){
      
      try {
        $fbuser = \helpers\MyFB::getStuff($sess->getToken());
        
        try {
          $user = new \models\FbUser(strval($fbuser['id']));
        } catch (Exception $e) {
          $notExist = $e->getCode() == 404;
        }
        if (!$notExist)
          throw new Exception('There is already a PrivateAsk account connected to that Facebook account');
        
        \models\FbUser::addFbLogin($GLOBALS['user'], $fbuser['id']);
        
        $_SESSION['connectFbSuccess'] = true;
        \helpers\Url::redirect('');
        
      } catch (Exception $e) {
        self::handleException($e);
      }
      
    } else {
      $loginUrl = \helpers\MyFB::getLoginUrl();
      
      \helpers\Url::redirect($loginUrl, true); // absoloute path
    }
  }
  
  public function disconnectFb(){
    $this->requireUser('loggedin');
    
    try {
      if ($GLOBALS['user']->hs_pass == '-')
        throw new Exception('You must set a password before disconnecting from Facebook');
      
      \models\FbUser::removeFbLogin($GLOBALS['user']);
      
      $_SESSION['removeFbSuccess'] = true;
      
      \helpers\Url::redirect('settings');
      
    } catch (Exception $e) {
      self::handleException($e);
      echo $e->getMessage(); // TODO STH BETTER
    }
  }
}
?>
