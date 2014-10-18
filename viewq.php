<!DOCTYPE html>
<html>
<head>
  <!-- ola ta links einai relative to /pa/. Aparaithto gia to /pa/question/123 -->
  <base href="/pa/">
  <title>View question - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="css/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <meta charset="UTF-8">
</head>
<body>

<?php

if (empty($_GET['qid']))
  terminate('Required parameters were not provided', 400);
if ((!is_numeric($_GET['qid'])) or ($_GET['qid'] <= 0))
  terminate('The question id is not of correct type.', 400);
$qid = intval($_GET['qid']);


$q = $con->query("SELECT * FROM questions WHERE id = $qid;")->fetch_array();

if (empty($q['touser']))
  terminate("The question you have requested does not exist or has been deleted.", 404);

$ownerName = $q['touser'];

$query = "SELECT deleteon, whosees FROM users WHERE username = '$ownerName';";
$owner = $con->query("SELECT * FROM users WHERE username = '$ownerName';")->fetch_array();

if ($owner['deleteon'] !== null)
  terminate('The owner of this question has deactivated their account.');

$res = $con->query("SELECT friend FROM friends WHERE `user` = '$ownerName' AND friend = '$user';");
$ownerHasUserFriend = (($user === $ownerName) or ($res and $res->num_rows > 0));

$whosees = $owner['whosees'];
if (empty($user) and $whosees !== 'all')
  terminate('You must log in to continue<br><a href="login.php">Log in</a>', 401);
if ($whosees === 'friends' and !$ownerHasUserFriend)
  terminate('Sorry, you do not have the right to see this question', 403);


if (empty($q['answer']))
  terminate('This question has not been answered yet.');


function printDate($prop){
  global $q;
  $time = strtotime($q[$prop]);
  $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
  return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
}

function printUser($prop){
  global $q;
  echo '<a href="user/'.$q[$prop].'">'.$q[$prop].'</a>';
}
?>
<div class="question aloneInPage" id="qContainer">
<div class="ui top attached tiny header">
To: <?=printUser('touser')?>
<a class="date">Asked: <?=printDate('timeasked')?></a><br>
<?php if ($q['publicasker'] and $q['fromuser'] !== 'deleteduser'){
  echo 'From: ';printUser('fromuser');} ?>
<a class="date">Answered: <?=printDate('timeanswered')?></a>
</div>
<div class="ui piled bottom attached segment"><div class="links">
<a href="reportq.php?qid=<?=$qid?>"><i class="red flag link icon"></i></a>
<?php if ($ownerName === $user) {
  echo '<br><a class="deleteq" href="deleteq.php?qid=';
  echo $qid .'"><i class="red trash link icon"></i></a>';
}?>
</div><h3 class="ui header"><?=$q['question']?></h3>
<p><?=$q['answer']?></p>
</div></div>

</body>
</html>
