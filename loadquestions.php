<?php

if (empty($ownerName)){
  if (empty($_GET['user']) or !isset($_GET['offset']))
    terminate('Required parameters were not provided', 400);
  
  $ownerName = $con->real_escape_string($_GET['user']);
  if ((!is_numeric($_GET['offset'])) or ($_GET['offset'] < 0))
    terminate('The question id is not of correct type.', 400);
  $offset = intval($_GET['offset']);
} else
  if (empty($offset)) $offset = 0;


//check if can view the content
$owner = $con->query("SELECT * FROM users WHERE username = '$ownerName';")->fetch_array();
if ($owner === null) terminate('This user does not exist or has deleted their account', 404);
$ownerFr = json_decode($owner['friends']);
if ($ownerFr === null) terminate('A server error has occurred.', 500);
array_push($ownerFr, $ownerName);
if ($owner['deleteon'] !== null)
  terminate('This user has deactivated their account.');
$see = $owner['whosees'];

if (empty($user) and $see !== 'all')
  die('<div class="warn">You must <a href="login.php">'.
    'log in</a> to view this user&#39;s questions<div>');
else if ($see === 'friends' and !in_array($user, $ownerFr))
  die();


function printDate($q, $prop){
  $time = strtotime($q[$prop]);
  $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
  return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
}
function printQ($q){
  global $user, $ownerName;
  echo '<div class="question"><div class="links">';
  echo '<a class="orange" href="reportq.php?qid='. $q['id'] .'">Flag</a>';
  if ($ownerName === $user)
    echo '<br><a class="red deleteq" href="deleteq.php?qid='. $q['id'] .'">Delete</a>';
  echo '</div>';
  if ($q['publicasker'] and ($q['fromuser'] !== 'deleteduser'))
    echo 'From: <a href="user/'.$q['fromuser'].'">'.$q['fromuser'] ."</a><br>";
  echo '<a class="date" href="question/'. $q['id'] .'">Answered: ';
  echo printDate($q, 'timeanswered') .'</a><br><h2>';
  echo $q['question'] .'</h2><p>'.$q['answer'] .'</p></div>';
  
}


$query = "SELECT * FROM questions WHERE timeanswered IS NOT NULL AND touser = '$ownerName' 
ORDER BY timeanswered DESC LIMIT 11 OFFSET $offset ;";
$res = $con->query($query);
if (!$res) echo 'A server error has occurred '.$con->error;
else if ($res->num_rows === 0)
  http_response_code(204); //No content
else {
  
  $i = 1;
  while(($row = $res->fetch_array()) and $i++ !== 11) 
    printQ($row);
  
  if ($isLast = ($res->num_rows < 11))
    echo '<div data-last="1"></div>';
}

?>
