<!DOCTYPE html>
<html>
<head>
<title>Pending questions - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="css/general.css">
<link rel="stylesheet" type="text/css" href="css/semantic.min.css">
</head>
<body>
<?php
if (empty($user))
  terminate('You are not logged in <a href="login.php">Log in</a>', 401);

$query = "SELECT * FROM questions WHERE touser = '$user'
  AND answer IS NULL ORDER BY timeasked LIMIT 50;";
$questions_raw = $con->query($query);

if (!$questions_raw) terminate('A server error has occurred '.$con->error, 500);
?>
<main id="qContainer">
  <div class="ui large info message"><i class="info icon"></i>
<?php

function printDate($q, $prop){
  $time = strtotime($q[$prop]);
  $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
  return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
}
function printQ($q){
  echo '<div class="question"><div class="ui top attached tiny header">';
  if ($q['publicasker'] and ($q['fromuser'] !== 'deleteduser'))
    echo 'From: <a href="user/'.$q['fromuser'].'">'.$q['fromuser'] .'</a>';
  echo '<a class="date" href="question/'. $q['id'] .'">Asked: ';
  echo printDate($q, 'timeasked') . '</a></div>';
  echo '<div class="ui piled bottom attached segment"><div class="links">';
  echo '<a href="answer.php?qid='. $q['id'] .'"><i class="pencil black link icon">';
  echo '</i></a><br><a class="deleteq" href="deleteq.php?qid=';
  echo $q['id'] .'"><i class="red trash link icon"></i></a></div>';
  echo '<h3 class="ui header">'. $q['question'] ."</h3></div></div>\n";
}

echo "Welcome back, $user! There ";

if ($questions_raw->num_rows === 0)
  echo 'are no more questions for you to answer</div>';
else {
  if ($questions_raw->num_rows === 1)
    echo 'is 1 question';
  else if ($questions_raw->num_rows === 50)
    echo 'are more than 50 questions';
  else
    echo 'are '.$questions_raw->num_rows.' questions';
  
  echo ' for you to answer</div>';
  
  while ($row = $questions_raw->fetch_array())
    printQ($row);
}
?>

</main>
</body>
</html>
