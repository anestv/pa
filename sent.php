<?php

if (isset($_POST['question']) and trim($_POST['question']))
  $question=$con->real_escape_string(htmlspecialchars($_POST["question"]));
else terminate('You did not enter a question', 400);

if (empty($_POST['pubAsk']) or $_POST['pubAsk'] != '1')
  $pubAsk = 0;
else $pubAsk = 1;

if (empty($_POST['to']))
  terminate('Required parameters were not provided', 400);

$ownerName = $con->real_escape_string($_POST['to']);
$owner = mysqli_fetch_array($con->query(
    "SELECT * FROM users WHERE username = '$ownerName';"));
if ($owner === null)
  terminate('This user does not exist or has deleted their account', 404);
$ownerFr = json_decode($owner['friends']);
if ($ownerFr === null) terminate('A server error has occurred.', 500);
array_push($ownerFr, $ownerName);
if ($owner['deleteon'] !== null)
  terminate('This user has deactivated their account.');

$ask = $owner['whoasks'];

//check permissions
if (empty($user) and $ask !== 'all')
  terminate('You must log in to ask a question', 401);
else if ($ask === 'friends' and !in_array($user, $ownerFr))
  terminate('Sorry, you cannot ask this user a question', 403);


if (empty($user)) {
  $user = 'anonymous';
  $pubAsk = 0;
}


if ($user === $ownerName) //TODO something better
  terminate('Are you problematic? Or do you have Down Syndrome? '.
      'Ask yourself please, why would you ask yourself?', 418);


$query = "INSERT INTO questions (fromuser, touser, question, publicasker)".
  "VALUES ('$user', '$ownerName', '$question', $pubAsk);";

$result = $con->query($query);

if ($result) echo "Your question has been submitted";
else terminate("Due to an unknown error your question was not submitted", 500);

?>