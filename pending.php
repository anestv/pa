<?php

if(empty($user))
  terminate('You are not logged in <a href="login.php">Log in</a>', 401);

$query = "SELECT * FROM questions WHERE touser = '$user' AND answer IS NULL ;";
$questions_raw = $con->query($query);

if(!$questions_raw) terminate('A server error has occurred', 500);
?>

<html>
<head>
<title>Pending questions - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
</head>
<body>

Hello, <?=$user?>

<div id="qContainer">

<?php
while($row = mysqli_fetch_array($questions_raw)) {
  echo '<div class="question"><div class="links"><a href="answer.php?qid='. $row['id']. '">Answer</a>';
  echo '<br><a class="red" href="delquest.php?qid='. $row['id']. '">Delete</a></div>';
  echo '<span class="date">Asked: '. $row['timeasked'] .'</span><h3>'. $row['question'] .'</h3></div>';
}
?>

</div>
</body>
</html>