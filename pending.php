<?php

if(empty($user))
  terminate('You are not logged in <a href="login.php">Log in</a>', 401);

$query = "SELECT * FROM questions WHERE touser = '$user'
  AND answer IS NULL ORDER BY timeasked LIMIT 50;";
$questions_raw = $con->query($query);

if (!$questions_raw) terminate('A server error has occurred '.$con->error, 500);
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

if ($questions_raw->num_rows === 0)
  echo '<div id="noQs">There are no more questions for you to answer!</div>';
else while ($row = $questions_raw->fetch_array()) {
  $timeasked = date('G:i \o\n l j/n/y', strtotime($row['timeasked']));
  echo '<div class="question"><div class="links"><a href="answer.php?qid=';
  echo $row['id']. '">Answer</a>'. '<br><a class="red" href="deleteq.php?qid=';
  echo $row['id']. '">Delete</a></div>'. '<a class="date">Asked: <time>';
  echo "$timeasked</time></a><h3>" . $row['question'] .'</h3></div>';
}
?>

</div>
</body>
</html>