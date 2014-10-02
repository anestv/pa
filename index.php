<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="application-name" content="PrivateAsk">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="css/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="css/index.css">
  <title>PrivateAsk</title>
</head>
<body>

<?php

if (!$user) {
  include_once("notLoggedIn.html");
  die;
}

function writeUnseen(){
  global $con, $user;
  
  $query = "SELECT COUNT(*) FROM questions WHERE touser = '$user' AND answer IS NULL;";
  $res = $con->query($query);
  $unseen = intval($res->fetch_array()[0]);
  if ($unseen > 99) $unseen = '99+';
  
  if ($unseen)
    return ' <div class="ui red label" id="unansweredCount">'.$unseen .'</div>';
  else return '';
}
?>

<nav class="ui seven item inverted fluid menu">
  <img class="item" src="res/logo.svg" alt="PrivateAsk logo" height="50">
  <a class="item" href="user/<?=$user?>"><i class="user icon"></i>Your profile</a>
  <a class="item" href="search.php"><i class="search icon"></i> Search</a>
  <a class="item" href="pending.php"><i class="question icon"></i>
    Pending questions<?=writeUnseen()?></a>
  <a class="item" href="settings.php"><i class="setting icon"></i>Settings</a>
  <a class="item" href="help.html" rel="help"><i class="help icon"></i>Help - FAQ</a>
  <a class="item" href="logout.php"><i class="sign out icon"></i>Log out</a>
</nav>

</body>
</html>
