<!DOCTYPE html>
<html lang="en">
<head>
<meta name="application-name" content="PrivateAsk">
<title>PrivateAsk</title>
</head>
<body>

<img src="res/logo.svg" alt="PrivateAsk logo" height="100">

<?php

if(empty($user)) {
  include_once("notLoggedIn.html");
  die();
}

echo "Hello, $user!";

?>

<a href="user/<?=$user?>">Your profile</a>
<a href="search.php">Search</a>
<a href="pending.php">Pending questions</a>
<a href="settings.php">Settings</a>

<a href="logout.php">Log out</a>

</body>
</html>