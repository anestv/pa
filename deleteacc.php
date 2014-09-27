<!DOCTYPE html>
<html>
<head>
  <title>Delete your account - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <link rel="stylesheet" type="text/css" href="css/semantic.min.css">
  <meta charset="utf-8">
  <style>
    .ui.red.inverted.block.header {
      background-image: linear-gradient(to bottom, #F05656, #CE4040);
    }
  </style>
</head>
<body>
<?php

if (empty($user))
  terminate('You must be logged in to delete your account', 401);

if ($_SERVER["REQUEST_METHOD"] === "POST"){ //thelei na to diagrapsei
  
  if (empty($_POST["user"]))
    terminate("You did not enter a username", 400);
  if ($_POST["user"] !== $user)
    terminate("You did not enter your account's username", 400);
  
  if (isset($_POST['pass']) and trim($_POST['pass']))
  	$pass = $_POST['pass'];
  else terminate('You did not enter a password', 400);
  
  $userobj = $con->query("SELECT hs_pass FROM users WHERE username = '$user';")->fetch_array();
  
  if (!password_verify($pass, $userobj['hs_pass']))
  	terminate('The password you entered is incorrect');
  

  $query = "UPDATE users SET deleteon = CURRENT_DATE + INTERVAL 7 DAY WHERE username = '$user';";
  $res = $con->query($query);
  
  if (!$res) terminate('A server error occurred '. $con->error, 500);
  else {
    session_destroy();
    echo '<div class="ui success message"><i class="trash icon"></i>';
	echo 'Your account will be deleted in 7 days. You have been logged out.';
	echo ' You will be redirected to the main page</div>';
    echo '<meta http-equiv="refresh" content="5; url=.">';
  }
}
?>
<main class="center940">

<h1 class="ui red inverted block top attached header">
  <i class="trash icon"></i> Delete your account
</h1>
<div class="ui attached segment">
  <p class="ui vertical segment">You are about to delete your account. Your profile and
   questions you have answered will be erased. Questions you have asked anonymously will
   be kept as they are, but questions you have asked publicly (i.e. your name appears in
   the question) will no longer show your name.</p>
  <p class="ui vertical segment">For a 7-day period the information mentioned above will
   be kept hidden, and will be recoverable by logging in your account. After that period,
   the changes described above will take place and they will be IRREVOCABLE and PERMANENT.
   We will not be able to recover any data after that period.</p>
  <p class="ui vertical segment">If you want to procceed, confirm the account's deletion
   by typing your username and password.</p>
</div>

<form method="post" class="ui bottom attached form segment">
  <div class="ui two fields">
    <div class="field"><div class="ui left labeled icon input">
      <i class="user icon"></i>
      <input type="text" placeholder="Username" name="user" required>
      <div class="ui corner label">
        <i class="red asterisk icon"></i>
      </div>
    </div></div>
    <div class="field"><div class="ui left labeled icon input">
      <i class="lock icon"></i>
      <input type="password" placeholder="Password" name="pass" required>
      <div class="ui corner label">
        <i class="red asterisk icon"></i>
      </div>
    </div></div>
  </div>
  <button type="submit" class="ui centered negative button">Delete</button>
</form>

</main>
</body>
</html>
