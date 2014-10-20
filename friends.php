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


if (isset($_POST['do'])){
  
  function setFriends($friendsJSON){
    global $con, $user;
    
    $friends = json_decode($_POST["friends"]);
    if (! ($friends and is_array($friends)))
      throw new InvalidArgumentException("Your friends were not provided as a correct JSON");
    
    if (isset($_POST["friendInput"]) and trim($_POST["friendInput"]))
      $friends[] = $_POST["friendInput"];
    
    $friends = array_filter($friends, "is_string"); //remove non-string elements
    
    $friends = array_unique($friends);
    
    //does he have himself as friend?
    $userinfriends = array_search($user, $friends);
    if ($userinfriends !== false)
      array_splice($friends, $userinfriends, 1);
    
    //clear previous friends
    $res = $con->query("DELETE FROM friends WHERE `user` = '$user';");
    if (!$res) throw new RuntimeException("MySQL error ".$con->error);
    
    $stmt = $con->prepare("INSERT INTO friends VALUES ('$user', ?);");
    
    $stmt->bind_param('s', $curr);
    
    foreach ($friends as $curr)
      $stmt->execute(); //$curr is registered and we dont have to bind it every time
    
    $stmt->close();
  }
  
  function changeAFriend($method, $friend){
    global $user, $con;
    
    if ($user === $friend)
      throw new InvalidArgumentException('You cannot add or remove yourself as friend');
    
    $friend = $con->real_escape_string($friend);
    
    if ($method === 'add')
      $query = "INSERT INTO friends VALUES ('$user', '$friend');";
    else
      $query = "DELETE FROM friends WHERE user = '$user' AND friend = '$friend';";
    
    $res = $con->query($query);
    
    if ($res and $con->affected_rows === 0)
      throw new Exception("$friend was not in your friends anyway");
    
    switch ($con->errno){
      case 1062:
        throw new Exception("You are already friends with $friend");
      case 1064:
        throw new RuntimeException('MySQL syntax error');
      case 1452:
        throw new Exception("It looks like $friend is not yet registered");
    }
  }
  
  
  $do = $_POST['do'];
  
  try {
    
    if (!(isset($_POST['friends']) and trim($_POST['friends'])))
      throw new InvalidArgumentException('Parameter friends was not given');
    
    if ($do === 'set')
      setFriends($_POST['friends']);
    else 
      changeAFriend($do, $_POST['friends']);
    
    successMsg('Success!', 'Your friends have been changed!');
    
  } catch (Exception $e){
    if ($e instanceof RuntimeException)
      http_response_code(500);
    else http_response_code(400);
    
    $excMsg = $e->getMessage();
    header("X-Error-Descr: $excMsg");
    echo '<div class="center480 ui warning message"><div class="header">';
    echo "Oops, something went wrong.</div><p>$excMsg</p></div>";
  }
  if ($requestAJAX)
    die('</body></html>'); //do not print View
}

$res = $con->query("SELECT friend FROM friends WHERE `user` = '$user';");
if (!$res) terminate("Querying database failed ".$con->error, 500);

$friends = array();
while ($curr = $res->fetch_array())
  $friends[] = $curr[0];

$friends_json_html = htmlspecialchars(json_encode($friends));

?>
<noscript>
  <div class="ui warning message">
    <div class="header"><i class="warning icon"></i> JavaScript is required for this page</div>
    If Javascript cannot be enabled, add your friends in JSON array format, and submit.
  </div>
  <form method="post">
    <input type="hidden" name="do" value="set">
    <textarea name="friends" cols="60" rows="4"><?=$friends_json_html?></textarea>
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

<form method="post" id="friendForm" class="scriptOnly">

<input type="hidden" name="do" value="set">
<input type="hidden" name="friends" value="<?=$friends_json_html?>">
<div class="ui action input">
  <input type="text" id="friendInput" name="friendInput" placeholder="Friend's username">
  <div id="addFriend" class="ui icon button"><i class="add icon"></i></div>
</div>

<div class="ui divided relaxed animated link list raised segment">
  <?php 
  foreach ($friends as $curr) {
    echo '<div class="item"><div class="ui right floated circular icon button">';
    echo '<i class="red remove icon"></i></div><a class="header" href="user/';
    echo $curr .'">'. "$curr</a></div>";
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
