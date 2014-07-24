<html>
<head>
<title>PrivateAsk</title>
</head>
<body>

<?php

if(empty($_SESSION['user'])) {
  include_once("notLoggedIn.html");
  die();
}

$user = $_SESSION['user'];
echo "Hello, $user!";

?>

<a href="user/<?=$user?>">Your profile</a>
<a href="pending.php">Pending questions</a>
<a href="settings.php">Settings</a>

<a href="login.php?logout=1">Log out</a>

</body>
</html>