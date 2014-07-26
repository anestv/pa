<html>
<head>
<title>Report question - PrivateAsk</title>
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


$question = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM questions WHERE id = $qid;"));


if (empty($question['touser']))
  terminate("The question you have requested does not exist or has been deleted.", 404);

if (empty($question['answer']))
  terminate('This question has not been answered yet.');


$ownerName = $question['touser'];
$owner = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM users WHERE username = '$ownerName';"));
$ownerFr = json_decode($owner['friends']);
if ($ownerFr === null) terminate('A server error has occurred.', 500);
array_push($ownerFr, $ownerName);
if ($owner['deleteon'] !== null)
  terminate('The owner of this question has deactivated their account.');


$whosees = $owner['whosees'];
if ($whosees === 'friends' and !in_array($user, $ownerFr))
  terminate('Sorry, you do not have the right to see this question', 403);


if ($_SERVER["REQUEST_METHOD"] === "POST"){
  if (empty($_POST['reason']))
    terminate("You did not tell us why you report this question");
  $reason = mysqli_real_escape_string($con, $_POST['reason']);
  
  if (!in_array($reason, array('illegal', 'threat', 'tos', 'porn', 'copyright', 'other')))
    terminate("Select one of the listed reasons", 400);

  
  //TODO other checks an xreiazetai
  
  $query = "INSERT INTO question_reports (qid, reporter, reason) VALUES ($qid, '$user', '$reason');";
  $res = mysqli_query($con, $query);
  if ($res) echo 'You have successfully reported this question';
  else terminate('This question could not be reported', 500);

} else { //dhladh einai GET
  
  //show the Q and A

  function printDate($prop){
    global $question;
    if (null === $question[$prop])
      terminate("A strange error regarding $prop occured", 500);
    $time = strtotime($question[$prop]);
    $res = '<span title="'. date('r', $time) . '">';
    $res .= date('G:i \o\n l j/n/y', $time) .'</span>';
    return $res;
  }
  
  echo 'To: '.$question['touser'].'<br>';
  if ($question['publicasker'] and $question['fromuser'] !== 'deleteduser')
    echo "From: ".$question['fromuser'] ."<br>";
  echo 'Answered: '. printDate('timeanswered').'<br><h2>';
  echo $question['question'].'</h2><p>'. $question['answer'] .'</p>';
}


if ($user === $ownerName)
  echo 'This question was asked to you, so we suggest you <a href="deleteq.php?qid='.
      .$qid . '">delete this question</a> if it offends or annoys you';
else 
  echo '
<form method="post">
<h3>What is wrong with this question / answer?</h3>
<input type="radio" id="ri1" name="reason" value="illegal" required><!--one of all is required-->
<label for="ri1">It contains illegal stuff</label><br>
<input type="radio" id="ri2" name="reason" value="threat">
<label for="ri2">It clearly threatens someone</label><br>
<input type="radio" id="ri3" name="reason" value="tos">
<label for="ri3">It violates the site&#39;s <a href="terms.html">Terms of Service</a></label><br>
<input type="radio" id="ri4" name="reason" value="porn">
<label for="ri4">It contains or links to porn</label><br>
<input type="radio" id="ri5" name="reason" value="copyright">
<label for="ri5">It contains copyrighted material</label><br>
<input type="radio" id="ri6" name="reason" value="other">
<label for="ri6">Something else</label><br>
<input type="submit"></form>
';

?>

</body>
</html>