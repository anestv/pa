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
  
  
  $users_raw = $con->query("SELECT username FROM users ;");
  if(!$users_raw) terminate("An error has occurred".$con->error, 500);
  $users = array();
  while($row = $users_raw->fetch_array())
    $users[] = $row['username']; //push to array
  
  
  $friends = json_decode($_POST["friends"]); //isws epikinduno alla to friends_str anti " exei \"
  if (! ($friends and is_array($friends)))
    terminate("Your friends were not provided as a correct JSON", 400);
  
  $friends = array_unique($friends);
  
  //mhpws exi ton eauto tou
  $userinfriends = array_search($user, $friends);
  if ($userinfriends !== false)
    array_splice($friends, $userinfriends, 1);
  
  foreach ($friends as $curr)
    if (! in_array($curr, $users))
      terminate("Some of the provided friends ($curr) do not have accounts", 400);
  
  
  $friends_str = $con->real_escape_string(json_encode($friends));
  
  $query = "UPDATE users SET friends = '$friends_str' WHERE username = '$user';";
  $result = $con->query($query);
  
  if (!$result)
    terminate("Your friends could not be changed ".$con->error, 500);
  else echo 'Your friends have been changed'; //TODO better
} 

$friends_raw = $con->query("SELECT friends FROM users WHERE username = '$user';");
if (! $friends_raw) terminate("Querying database failed ".$con->error, 500);
$friends_json = $friends_raw->fetch_array()['friends']; //in JSON
if (empty($friends)) $friends = json_decode($friends_json);

?>
<noscript>JavaScript is required for this page</noscript>

<form method="post">
<input type="hidden" name="friends" value="<?=htmlspecialchars($friends_json)?>"><br>
<input type="text" id="friendInput" name="friendInput" placeholder="Friends">
<button type="button" id="addFriend">+</button><br>
<!-- must be type=button or it will submit the form-->

<ul id="friendList">
<?php 
foreach ($friends as $curr)
  echo "<li>$curr";
?>
</ul>
<br>
<input type="submit">
</form>
<script src="js/jquery2.min.js"></script>
<script src="js/friends.js"></script>

</body>
</html>