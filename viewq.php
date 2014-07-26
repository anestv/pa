<html>
<head>
<!-- ola ta links einai relative to /pa/. Aparaithto gia to /pa/question/123 -->
<base href="/pa/">
<title>View question - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
<meta charset="UTF-8">
</head>
<body>

<?php

if (empty($_GET['qid']))
  terminate('Required parameters were not provided', 400);
if ((!is_numeric($_GET['qid'])) or ($_GET['qid'] <= 0))
  terminate('The question id is not of correct type.', 400);
$qid = intval($_GET['qid']);


$q = mysqli_fetch_array($con->query("SELECT * FROM `questions` WHERE `id` = $qid;"));

if (empty($q['touser']))
  terminate("The question you have requested does not exist or has been deleted.", 404);
$ownerName = $q['touser'];

$owner = mysqli_fetch_array($con->query("SELECT * FROM `users` WHERE `username` = '$ownerName';"));
$ownerFr = json_decode($owner['friends']);
if ($ownerFr === null) terminate('A server error has occurred.', 500);
array_push($ownerFr, $ownerName);
if ($owner['deleteon'] !== null)
  terminate('The owner of this question has deactivated their account.');


$whosees = $owner['whosees'];
if (empty($user) and $whosees !== 'all')
  terminate('You must log in to continue<br><a href="login.php">Log in</a>', 401);
if ($whosees === 'friends' and !in_array($user, $ownerFr))
  terminate('Sorry, you do not have the right to see this question', 403);


if (empty($q['answer']))
  terminate('This question has not been answered yet.');


function printDate($prop){
  global $q;
  if (null === $q[$prop])
    terminate("A strange error regarding $prop occured", 500);
  $time = strtotime($q[$prop]);
  $res = '<span title="'. date('r', $time) . '">';
  $res .= date('G:i \o\n l j/n/y', $time) .'</span>';
  return $res;
}
?>

<div class="question">
<div class="links">
<a class="orange" href="reportq.php?qid=<?=$qid?>">Flag as inappropriate</a>
<br>
<?php if ($ownerName === $user)
  echo '<a class="red" href="deleteq.php?qid='.$qid .'">Delete this question</a>';
?></div>

To: <a href="user/<?=$q['touser']?>"><?=$q['touser']?></a><br>
<?php if ($q['publicasker'] and $q['fromuser'] !== 'deleteduser')
  echo 'From: <a href="user/'.$q['fromuser'].'">'.$q['fromuser'] ."</a><br>"; ?>
Asked: <?=printDate('timeasked')?><br>
Answered: <?=printDate('timeanswered')?><br>

<h2><?=$q['question']?></h2>
<p><?=$q['answer']?></p>

</div>
</body>
</html>