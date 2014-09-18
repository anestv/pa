<html>
<head>
<title>Delete question - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="css/general.css">
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


$question = $con->query("SELECT touser FROM questions WHERE id = $qid;")->fetch_array();

if (empty($question['touser']))
  terminate("The question you have requested does not exist or has been deleted.", 404);


if ($question['touser'] !== $user)
  terminate('You cannot delete this question', 403);

if (empty($_GET['del'])){ ?>

<form>
  <input type="hidden" name="qid" value="<?=$qid?>">
  <input type="hidden" name="del" value="1">
  <h2>Are you sure you want to delete this question?</h2>
  <h4>This cannot be undone</h4>
  <a id="butCancel" href="index.php" onclick="history.back();return history.length<2;">Cancel</a> <!-- TODO!! XSS this is unsafe-inline -->
  <input type="submit" class="red" value="Delete">
</form>

<?php } else {
  
  $del = $con->query("DELETE FROM questions WHERE id = $qid;");
  if ($del)
    echo '<div id="success">You have successfully deleted this question</div>';
  else terminate('The question was not deleted'. $con->error, 500);
}

?>

</body>
</html>