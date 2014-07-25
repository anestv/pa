<html>
<head>
<title>Delete question - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
</head>
<body>

<?php

if (empty($_GET['qid']))
  terminate('Required parameters were not provided', 400);
else if((!is_numeric($_GET['qid'])) or ($_GET['qid'] <= 0))
  terminate('The question id is not of correct type.', 400);
else
  $qid = intval($_GET['qid']);

if (empty($user))
  terminate('You must log in to continue<br><a href="login.php">Log in</a>', 401);


$question = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `questions` WHERE `id` = $qid;"));

if (empty($question['touser']))
  terminate("The question you have requested does not exist or has been deleted.", 404);
$owner = $question['touser'];


if ($owner !== $user)
  terminate('You cannot delete this question', 403);

$del = mysqli_query($con, "DELETE FROM `questions` WHERE `id` = $qid;");
if ($del) echo 'You have successfully deleted this question';
else terminate('The question could not be deleted', 500);

?>

</body>
</html>