<html>
<head>
<title>PrivateAsk</title>
</head>
<body>

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