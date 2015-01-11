<?php namespace controllers;
use core\view as View;
use \Exception;

class Friends extends \core\controller {
  
  public function get(){
    $this->requireUser('loggedin');
    
    try {
      $data['title'] = 'Edit your friends';
      $data['bodyData'] = 'data-user="'.$GLOBALS['user']->username.'"';
      $data['scripts'] = ['jquery' => 1, 'custom' => ['friends.js']];
      
      $data['friends'] = $GLOBALS['user']->getFriends();
      
    } catch (Exception $e) {
      self::handleException($e);
    }
    
    View::rendertemplate('header', $data);
    View::render('friends', $data);
    View::rendertemplate('footer', $data);
  }
  
  public function post(){
    $this->requireUser('loggedin');
    
    try {
      if (empty($_POST['friends']))
        throw new Exception('No friends were provided');
      
      // this is in controller so that we can add friendInput if needed
      $friends = json_decode($_POST['friends']);
      if (! ($friends and is_array($friends)))
        throw new InvalidArgumentException("Your friends were not provided as a correct JSON");
      
      if (isset($_POST['friendInput']) and trim($_POST['friendInput']))
        $friends[] = $_POST['friendInput'];
      
      $GLOBALS['user']->editFriends('set', $friends);
      
      $GLOBALS['friendsSuccess'] = true;
      
    } catch (Exception $e) {
      self::handleException($e);
    }
    
    $this->get();
  }
}
