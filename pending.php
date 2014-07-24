<?php

if(empty($_SESSION["user"]))
  terminate('You are not logged in <a href="login.php">Log in</a>', 401);

//upothetw oti o user einai egkuro
$user = $_SESSION["user"]; //mhpws na to kanw escape einai hdh escaped apo to login

$query = "SELECT * FROM `questions` WHERE `touser` = '$user' AND `answer` IS NULL ;";
$questions_raw = mysqli_query($con, $query);

if(!$questions_raw) terminate('A server error has occurred', 500);

$questions = array();
while($row = mysqli_fetch_array($questions_raw)) {
  $curr = array('id'=> intval($row['id']), 'q'=>$row['question'], 't'=> $row['timeasked']);
  array_push($questions, $curr);
}

?>

<html>
<head>
<title>Pending questions - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
</head>
<body>

Hello, <?=$user?>

<div id="qcont">

<?php
foreach ($questions as $curr){
  echo '<div class="question"><div class="links"><a href="answer.php?qid='. $curr['id']. '">Answer</a>';
  echo '<br><a class="red" href="delquest.php?qid='. $curr['id']. '">Delete</a></div>';
  echo '<span class="date">Asked: '. $curr['t'] .'</span><h3>'. $curr['q'] .'</h3></div>';
//.$curr['q'].'<br>Asked: '.$curr['t'].' </div>';
}
echo 'done!';
?>

</div>
</body>
</html>