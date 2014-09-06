<html>
<head>
<title>Answer - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
</head>
<body>

<?php

if ($user == '')
  terminate('You must be logged in to answer a question <br><a href="login.php">Log in</a>', 401);

if (empty($_GET['qid']))
  terminate('Required parameters were not provided', 400);
else if((!is_numeric($_GET['qid'])) or ($_GET['qid'] <= 0))
  terminate('The question id is not of correct type.', 400);
else
  $qid = intval($_GET['qid']);


$q = $con->query("SELECT * FROM questions WHERE id = $qid;")->fetch_array();

if(empty($q['touser']))
  terminate("The question you have requested does not exist or has been deleted.", 404);

if($q['touser'] !== $user)
  terminate("This question was not asked to you, so you cannot answer it.", 403);

if(!empty($q['answer'])) //an exei hdh apanthsh
  terminate("You have already answered this question; you can't reanswer it", 405);


if(isset($_POST['answer']) and trim($_POST['answer'])) {
  $answer = $con->real_escape_string(htmlspecialchars($_POST['answer']));
  
  $query = "UPDATE questions SET answer = '$answer', timeanswered = NOW() WHERE id = $qid ;";
  $result = $con->query($query);

  if ($result) die("You have successfully answered!"); //TODO sth better
  else terminate("Due to an unknown error your answer was not submitted", 500);
}

function printDate($prop){
  global $q;
  $time = strtotime($q[$prop]);
  $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
  return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
}

?>

<div class="question" id="qContainer">
<div class="links">
<a class="red" href="deleteq.php?qid=<?=$qid?>">Delete this question</a>
</div>

<?php if ($q['publicasker'] and $q['fromuser'] !== 'deleteduser')
  echo 'From: <a href="user/'.$q['fromuser'].'">'.$q['fromuser'] ."</a><br>"; ?>
Asked: <?=printDate('timeasked')?><br>

<h2><?=$q['question']?></h2>

</div>
<br>
<form method="post">
<textarea name="answer" placeholder="Your answer" required autofocus maxlength="200"></textarea>
<br><input type="submit">
</form>

</body>
</html>