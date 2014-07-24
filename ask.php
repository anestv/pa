<html>
<head>
<title>PrivateAsk - Ask</title>
<link rel="stylesheet" type="text/css" href="general.css">
</head>
<body>

<?php

if (empty ($_SESSION['user']))
  terminate('You must be logged in to ask a question <br><a href="login.php">Log in</a>', 401);

?>

<form method="post" action="sent.php">
<input type="text" name="to" placeholder="To username" required maxlength="20">
<br>
<textarea name="question" placeholder="Your question" required maxlength="200"></textarea>
<br>
<input type="submit">
</form>

</body>
</html>