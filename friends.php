<html>
<head>
<title>Friends - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
</head>
<body data-user="<?=$user?>">

<?php

if (empty($user))
  terminate('You must be logged in <br><a href="login.php">Log in</a>', 401);


if ($_SERVER["REQUEST_METHOD"] === "POST"){
  
  if (!(isset($_POST["friends"]) and trim($_POST["friends"])))
    terminate("Parameter friends was not given", 400);
  
  
  $users_raw = mysqli_query($con, "SELECT `username` FROM `users`;");
  if(!$users_raw) terminate("An unknown error has occurred", 500);
  $users = array();
  while($row = mysqli_fetch_array($users_raw)) {
    array_push($users, $row['username']);
  }
  
  $friends = json_decode($_POST["friends"]); //isws epikinduno alla to friends_str anti " exei \"
  if (! ($friends and is_array($friends)))
    terminate("Your friends were not provided as a correct JSON", 400);
  
  if ($friends != array_unique($friends))
    $friends = array_unique($friends);
  
  
  //mhpws exi ton eauto tou
  $userinfriends = array_search($user, $friends);
  if ($userinfriends !== false)
    array_splice($friends, $userinfriends, 1);
  
  foreach ($friends as $curr){
    if (! in_array($curr, $users))
      terminate("Some of the provided friends ($curr) do not have accounts", 400);
  }
  
  $friends_str = mysqli_real_escape_string($con, json_encode($friends));
  
  $query = "UPDATE `users` SET `friends` = '$friends_str' WHERE `username` = '$user';";
  $result = mysqli_query($con, $query);
  
  if (!$result)
    terminate("Due to an unknown error your friends could not be changed", 500);
  else echo 'Your friends have been changed'; //TODO better
} 

$friends_raw = mysqli_query($con,"SELECT `friends` FROM `users` WHERE `username` = '$user';");
if (! $friends_raw) terminate("Querying database failed", 500);
$friends_json = mysqli_fetch_array($friends_raw)['friends']; //in JSON
if (empty($friends)) $friends = json_decode($friends_json);


?>
<noscript>JavaScript is required for this page</noscript>

<form method="post" name="af">
<input type="hidden" name="friends" value="<?=htmlspecialchars($friends_json)?>"><br>
<input type="text" id="friendInput" name="friendInput" placeholder="Friends">
<button type="button" name="addFr">+</button><br>

<output name="friendList"><ul>
<?php 
foreach ($friends as $curr)
  echo '<li>'. htmlspecialchars($curr);
?>
</ul>
</output><br>
<input type="submit">
</form>
<script src="friends.js"></script>

</body>
</html>