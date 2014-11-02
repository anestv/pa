<?php

try {
  
  if (isset($_POST['question']) and trim($_POST['question']))
    $question = $con->real_escape_string(htmlspecialchars($_POST["question"]));
  else throw new Exception('You did not enter a question', 400);
  
  if (empty($_POST['pubAsk']))
    $pubAsk = 0;
  else $pubAsk = 1;
  
  if (empty($_POST['to']))
    throw new Exception('Required parameters were not provided', 400);
  
  $ownerName = $con->real_escape_string($_POST['to']);
  
  $query = "SELECT whoasks, deleteon FROM users WHERE username = '$ownerName';";
  $owner = $con->query($query)->fetch_array();
  
  if ($owner === null)
    throw new Exception('This user does not exist or has deleted their account', 404);
  
  if ($owner['deleteon'] !== null)
    throw new Exception('This user has deactivated their account.', 404);
  
  $ask = $owner['whoasks'];
  
  $res = $con->query("SELECT friend FROM friends WHERE `user` = '$ownerName' AND friend = '$user';");
  if (!$res) throw new RuntimeException($con->error, 500);
  $ownerHasUserFriend = (($user === $ownerName) or ($res and $res->num_rows > 0));
  
  //check permissions
  if (empty($user) and $ask !== 'all')
    throw new Exception('You must <a href="login.php">log in</a> to ask a question', 401);
  else if ($ask === 'friends' and !$ownerHasUserFriend)
    throw new Exception('Sorry, you cannot ask this user a question', 403);
  
} catch (Exception $e) {
  terminate($e->getMessage(), $e->getCode());
}

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
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.19.3/css/semantic.min.css">
<meta charset="utf-8"></head><body>';

if ($result){
  successMsg('Your question has been submitted');
  if (!$requestAJAX) redirect("user/$ownerName");
} else
  errorMsg("Your question was not submitted", $con->error);

if (!$requestAJAX) echo '</body></html>';
?>
