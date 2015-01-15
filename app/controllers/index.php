<?php namespace controllers;
use core\view as View;

class index extends \core\controller{
  
  public function index(){  
    
    if (empty($_SESSION['user'])){
      $this->notLoggedIn();
      return;
    }
    
    $user = $GLOBALS['user']->username;
    
    $query = <<<QUERY
    SELECT * FROM questions WHERE
      (answer IS NOT NULL) AND
      (touser IN (
        SELECT username FROM users WHERE
          (deleteon IS NULL) AND
          (
            whosees = 'all' OR
            whosees = 'users' OR
            username IN (
              SELECT `user` FROM friends WHERE friend = '$user'
            )
          )
      )) AND
      (touser IN (
        SELECT friend FROM friends WHERE `user` = '$user'
      ))
    ORDER BY timeanswered DESC
    LIMIT 30;
QUERY;
    /*
      questions must be answered, user must have friended touser,
      touser must allow user to see the question,
      touser must not be deactivated, sort newest to oldest, get 30 newest
    */
    
    $qset = new \models\QuestionSet($query);
    $data['questions'] = $qset->members;
    $data['scripts'] = ['jquery' => 1, 'jquery.age' => 1, 'custom' => ['qfeed.js']];
    
    View::rendertemplate('header',$data);
    View::render('index', $data);
    View::rendertemplate('footer',$data);
  }
  
  private function notLoggedIn(){
    
    $data['styles'] = ['notLoggedIn.css'];
    
    if (ENABLE_FACEBOOK){
      \helpers\MyFB::setPath('api/facebooklogin');
      $data['fbLoginUrl'] = \helpers\MyFB::$facebook->getLoginUrl();
    }
    
    View::rendertemplate('header', $data);
    View::render('notLoggedIn', $data);
    View::rendertemplate('footer', $data);
  }
}
