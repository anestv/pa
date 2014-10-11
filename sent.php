<?php

if (isset($_POST['question']) and trim($_POST['question']))
  $question = $con->real_escape_string(htmlspecialchars($_POST["question"]));
else terminate('You did not enter a question', 400);

if (empty($_POST['pubAsk']))
  $pubAsk = 0;
else $pubAsk = 1;

if (empty($_POST['to']))
  terminate('Required parameters were not provided', 400);

$ownerName = $con->real_escape_string($_POST['to']);
$owner = $con->query("SELECT whoasks, friends, deleteon FROM users".
  " WHERE username = '$ownerName';")->fetch_array();
if ($owner === null)
  terminate('This user does not exist or has deleted their account', 404);
$ownerFr = json_decode($owner['friends']);
if ($ownerFr === null) terminate('A server error has occurred.', 500);
array_push($ownerFr, $ownerName);
if ($owner['deleteon'] !== null)
  terminate('This user has deactivated their account.', 404);

$ask = $owner['whoasks'];

//check permissions
if (empty($user) and $ask !== 'all')
  terminate('You must log in to ask a question', 401);
else if ($ask === 'friends' and !in_array($user, $ownerFr))
  terminate('Sorry, you cannot ask this user a question', 403);


if (empty($user)) {
  $user = 'anonymous';
  $pubAsk = 0;
} else if ($user === $ownerName)
  $pubAsk = 1;


$query = "INSERT INTO questions (fromuser, touser, question, publicasker)".
 " VALUES ('$user', '$ownerName', '$question', $pubAsk);";

$result = $con->query($query);

$requestAJAX = isset(apache_request_headers()['X-Requested-With']) and 
    apache_request_headers()['X-Requested-With'] === "XMLHttpRequest";

if (!$requestAJAX)
  echo '<!DOCTYPE html><html><head><title>Ask a question - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="css/general.css">
<link rel="stylesheet" type="text/css" href="css/semantic.min.css">
<meta charset="utf-8"></head><body>';

if ($result){
  successMsg('Your question has been submitted');
  if (!$requestAJAX) redirect("user/$ownerName");
} else
  errorMsg("Your question was not submitted", $con->error);

if (!$requestAJAX) echo '</body></html>';
?>
