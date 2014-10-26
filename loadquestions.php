<?php

try {
  
  if (empty($ownerName)){ //if not included from profile.php
    if (empty($_GET['user']) or !isset($_GET['offset']))
      throw new Exception('Required parameters were not provided');
    
    $ownerName = $con->real_escape_string($_GET['user']);
    
    $res = $con->query("SELECT friend FROM friends WHERE `user` = '$ownerName' AND friend = '$user';");
    $ownerHasUserFriend = (($user === $ownerName) or ($res and $res->num_rows > 0));
    
    if (empty($_GET['offset']))
      throw new InvalidArgumentException('Required parameters were not provided');
    if ((!is_numeric($_GET['offset'])) or ($_GET['offset'] < 0))
      throw new InvalidArgumentException('The offset is not of correct type.');
    $offset = intval($_GET['offset']);
    
  } else
    if (empty($offset)) $offset = 0;
  
  
  //check if can view the content
  $owner = $con->query("SELECT * FROM users WHERE username = '$ownerName';")->fetch_array();
  if ($owner === null)
    throw new Exception('This user does not exist or has deleted their account');
  
  if ($owner['deleteon'] !== null)
    throw new Exception('This user has deactivated their account.');
  
  $see = $owner['whosees'];
  
  if (empty($user) and $see !== 'all'){
    if ($requestAJAX)
      $excMsg = 'You must log in';
    else
      $excMsg = 'You must <a href="login.php">log in</a> to view this user\'s questions';
    
    throw new Exception($excMsg);
    
  } else if ($see === 'friends' and !$ownerHasUserFriend) {
    if ($requestAJAX)
      throw new Exception("You are not allowed to view this user's questions");
    else return;
  }
  
  $query = "SELECT * FROM questions WHERE timeanswered IS NOT NULL AND touser".
    " = '$ownerName' ORDER BY timeanswered DESC LIMIT 11 OFFSET $offset ;";
  $res = $con->query($query);
  if (!$res)
    throw new RuntimeException($con->error);
  
} catch (Exception $e) {
  handleException($e, ''); //no header at the ui warning message
  return;
  // return stops the current file. If included from profile.php,
  // execution will be returned there, otherwise same as exit()
}

function printDate($q, $prop){
  $time = strtotime($q[$prop]);
  $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
  return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
}
function printQ($q){
  global $user, $ownerName;
  
  echo '<div class="question"><div class="ui top attached tiny header">';
  if ($q['publicasker'] and ($q['fromuser'] !== 'deleteduser'))
    echo 'From: <a href="user/'.$q['fromuser'].'">'.$q['fromuser'] .'</a>';
  echo '<a class="date" href="question/'. $q['id'] .'">Answered: ';
  echo printDate($q, 'timeanswered') . '</a></div>';
  echo '<div class="ui piled bottom attached segment"><div class="links">';
  echo '<a href="reportq.php?qid='.$q['id'].'"><i class="red flag link icon"></i></a>';
  if ($ownerName === $user) {
    echo '<br><a class="deleteq" href="deleteq.php?qid=';
    echo $q['id'] .'"><i class="red trash link icon"></i></a>';
  }
  echo '</div><h3 class="ui header">' . $q['question'] . '</h3>';
  echo '<p>'. $q['answer'] . '</p></div></div>';
}

if ($res->num_rows === 0)
  http_response_code(204); //No content
else {
  
  $i = 1;
  while(($row = $res->fetch_array()) and $i++ !== 11) 
    printQ($row);
  
  if ($isLast = ($res->num_rows < 11))
    echo '<div data-last="1"></div>';
}

?>
