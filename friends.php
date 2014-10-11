<!DOCTYPE html>
<html>
<head>
  <title>Friends - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <link rel="stylesheet" type="text/css" href="css/semantic.min.css">
  <meta charset="utf-8">
</head>
<body data-user="<?=$user?>">

<?php

if (empty($user))
  terminate('You must be logged in <br><a href="login.php">Log in</a>', 401);


if ($_SERVER["REQUEST_METHOD"] === "POST"){
  
  if (!(isset($_POST["friends"]) and trim($_POST["friends"])))
    terminate("Parameter friends was not given", 400);
  
  
  $users_raw = $con->query("SELECT username FROM users;");
  if (!$users_raw) terminate("An error has occurred".$con->error, 500);
  $users = array();
  while($row = $users_raw->fetch_array())
    $users[] = $row['username']; //push to array
  
  
  $friends = json_decode($_POST["friends"]); //isws epikinduno alla anti " exei \"
  if (! ($friends and is_array($friends)))
    terminate("Your friends were not provided as a correct JSON", 400);
  
  if (isset($_POST["friendInput"]) and trim($_POST["friendInput"]))
    $friends[] = $_POST["friendInput"];
  
  $friends = array_unique($friends);
  
  //mhpws exi ton eauto tou
  $userinfriends = array_search($user, $friends);
  if ($userinfriends !== false)
    array_splice($friends, $userinfriends, 1);
  
  foreach ($friends as $curr)
    if (! in_array($curr, $users)) //case-sensitive
      terminate("Some of the provided friends ($curr) do not have accounts", 400);
  
  
  $friends_str = $con->real_escape_string(json_encode($friends));
  
  $query = "UPDATE users SET friends = '$friends_str' WHERE username = '$user';";
  $result = $con->query($query);
  
  if (!$result)
    terminate("Your friends could not be changed ".$con->error, 500);
  else 
    echo '<div class="ui success message"><i class="checkmark icon">'.
      '</i>Your friends have been changed</div>';
}

$friends_raw = $con->query("SELECT friends FROM users WHERE username = '$user';");
if (! $friends_raw) terminate("Querying database failed ".$con->error, 500);
$friends_json = $friends_raw->fetch_array()['friends']; //in JSON
if (empty($friends)) $friends = json_decode($friends_json);

?>
<noscript>
  <div class="ui warning message">
    <div class="header"><i class="warning icon"></i> JavaScript is required for this page</div>
    If Javascript cannot be enabled, add list your friends in JSON format, and submit.
  </div>
  <form method="post">
    <textarea name="friends"><?=htmlspecialchars($friends_json)?></textarea>
    <input type="submit">
  </form>
</noscript>

<div class="ui info message">
  <i class="close icon"></i>
  <div class="header"><i class="info icon"></i> Here you can edit your friends</div>
  <ul class="list">
    <li>To add a friend, enter the exact username in the field, press
      Enter and click the Save button when done. If you don't know the
      exact username, use <a href="search.php">Search</a> to find it.
    <li>To remove a friend, click the (x) button next to the username.
    <li>You can click at any username to view that profile.
      Please save your friends before doing so.
  </ul>
</div>

<form method="post" id="friendForm">

<input type="hidden" name="friends" value="<?=htmlspecialchars($friends_json)?>">
<div class="ui action input">
  <input type="text" id="friendInput" name="friendInput" placeholder="Friend's username">
  <div id="addFriend" class="ui icon button"><i class="add icon"></i></div>
</div>

<div class="ui divided relaxed animated link list raised segment">
  <?php 
  foreach ($friends as $curr) {
    echo '<div class="item"><div class="ui right floated circular icon button">';
    echo '<i class="red remove icon"></i></div><a class="header" href="user/';
    echo $curr .'">'. $curr .'</a></div>';
  } ?>
</div>

<button type="submit" class="ui positive centered labeled icon button">
  <i class="save icon"></i>Save friends
</button>
</form>

<script src="js/jquery2.min.js"></script>
<script src="js/friends.js"></script>

</body>
</html>
