<!DOCTYPE html>
<html lang="en">
<head>
<meta name="application-name" content="PrivateAsk">
<link rel="stylesheet" type="text/css" href="semantic.min.css">
<title>PrivateAsk</title>
</head>
<body>

<img src="res/logo.svg" alt="PrivateAsk logo" height="100" style="
    right: 30px;
    position: absolute;
">

<?php

if (!$user) {
  include_once("notLoggedIn.html");
  die();
}

echo "Hello, $user!";

?>

<div class="ui buttons">
<a class="ui blue button" href="user/<?=$user?>"><i class="user icon"></i>Your profile</a>
<a class="ui teal button" href="search.php"><i class="search icon"></i>Search</a>
<a class="ui green button" href="pending.php"><i class="question icon"></i>Pending questions</a>
<a class="ui purple button" href="settings.php"><i class="setting icon"></i>Settings</a>
<a class="ui vk button" href="help.html" rel="help"><i class="help icon"></i>Help - FAQ</a>
<a class="ui button" href="logout.php"><i class="sign out icon"></i>Log out</a>
</div>

</body>
</html>
