<?php

if (empty($ownerName)){
  if (empty($_GET['user']) or !isset($_GET['offset']))
    terminate('Required parameters were not provided', 400);
  
  $ownerName = mysqli_real_escape_string($con, $_GET['user']);
  if ((!is_numeric($_GET['offset'])) or ($_GET['offset'] < 0))
    terminate('The question id is not of correct type.', 400);
  $offset = intval($_GET['offset']);
} else
  if (empty($offset)) $offset = 0;


function printDate($q, $prop){
  $time = strtotime($q[$prop]);
  $res = '<span title="'. date('r', $time) . '">';
  $res .= date('G:i \o\n l j/n/y', $time) .'</span>';
  return $res;
}
function printQ($q, $isLast){//TODO isLast
  global $user, $ownerName;
  echo '<div class="question"'. ($isLast? ' data-last="1"':'').'><div class="links">';
  echo '<a class="orange" href="reportq.php?qid='. $q['id'] .'">Flag</a>';
  if ($ownerName === $user)
    echo '<br><a class="red" href="deleteq.php?qid='. $q['id'] .'">Delete</a>';
  echo '</div>';
  if ($q['publicasker'] and ($q['fromuser'] !== 'deleteduser'))
    echo 'From: <a href="user/'.$q['fromuser'].'">'.$q['fromuser'] ."</a><br>";
  echo '<a class="date" href="question/'. $q['id'] .'">Answered: ';
  echo printDate($q, 'timeanswered') .'</a><br><h2>';
  echo $q['question'] .'</h2><p>'.$q['answer'] .'</p></div>';
  
}


$query = "SELECT * FROM `questions` WHERE timeanswered IS NOT NULL AND touser = '$ownerName' 
ORDER BY timeanswered DESC LIMIT 11 OFFSET $offset ;";
$res = mysqli_query($con, $query);
if (!$res) echo 'A server error has occurred';
else if (mysqli_num_rows($res) === 0)
  http_response_code(204); //No content
else {
  $num = mysqli_num_rows($res);
  $i = 1;
  while(($row = mysqli_fetch_array($res)) and $i !== 11) {
    printQ($row, ($isLast = $num === $i++)); //dhladh an auto einai to teleutaio
  }
}


?>
