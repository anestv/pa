<!DOCTYPE html>
<html>
<head>
  <title>Login - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <link rel="stylesheet" type="text/css" href="css/semantic.min.css">
  <meta charset="utf-8">
</head>
<body>

<main class="center940">
<?php

if (!empty($_REQUEST['loggedOut']) and empty($user)) //came from logout.php
  echo '<div class="ui info message"><h2 class="header">'.
  '<i class="sign out icon"></i> You have been logged out</h2>'.
  'We home to see you soon!</div>';
  
else if($user) { //hdh sundedemenos
  
  echo '<meta http-equiv="refresh" content="4;url=index.php">';
  die("Hello $user, you are already logged in");
  
} else if ($_SERVER["REQUEST_METHOD"] === "POST"){ //thelei na sundethei
  
  if (isset($_POST["user"]) and trim($_POST["user"]))
    $user = $con->real_escape_string($_POST["user"]);
  else terminate("You did not specify a username", 400);
  
  if (isset($_POST['pass']) and trim($_POST['pass']))
  	$pass = $_POST['pass'];
  else terminate('You did not enter a password', 400);
  
  
  $user_dbraw = $con->query("SELECT username , hs_pass FROM users WHERE username = '$user';");
  if (! $user_dbraw) terminate("Querying database failed: ".$con->error, 500);
  $user_db = $user_dbraw->fetch_array();
  
  if($user_db['username'] == "")
    terminate('This user does not exist. Maybe you should <a href="register.php">register</a>');
  
  if ($user === $user_db['username']){
  	if (!password_verify($pass, $user_db['hs_pass']))
  	  terminate('The password you entered is incorrect');
  	
    //apotroph diagrafhs
    $con->query("UPDATE users SET deleteon = NULL WHERE username = '$user';");
    if ($con->affected_rows > 0)
      echo 'Your account has been recovered! Welcome back!';//den tha to diavasei giati tha ginei redirect

    if (!empty($_POST["keep"]))
      session_set_cookie_params(60*60*24*7); //1 evdomada
    session_regenerate_id(true);
    $_SESSION['user'] = $user;
    
    if (isset($_SERVER['HTTP_REFERER']) and substr($_SERVER['HTTP_REFERER'], -4) === '/pa/')
      header("Location: http://".$_SERVER['HTTP_HOST']."/pa/", true, 302);
    else echo '<meta http-equiv="refresh" content="0;url=index.php">';
  }
}
?>

<div class="center480">
  <h3 class="ui orangeBg top attached header">Log in to <i>PrivateAsk</i></h3>
  <form action="login.php" method="POST" class="ui attached form segment">
    <div class="ui left labeled field icon input">
      <input name="user" required type="text" placeholder="Username" tabindex="1" autofocus>
      <i class="user icon"></i>
      <div class="ui corner label"><i class="red asterisk icon"></i></div>
    </div>
    <div class="ui left labeled field icon input">
      <input name="pass" required type="password" placeholder="Password" tabindex="2">
      <i class="lock icon"></i>
      <div class="ui corner label"><i class="red asterisk icon"></i></div>
    </div>
    <button type="submit" class="ui right floated labeled icon positive button" tabindex="4">
      <i class="sign in icon"></i>Log in
    </button>
    <div class="ui toggle checkbox">
      <input type="checkbox" name="keep" id="keep" tabindex="3">
      <label for="keep">Keep me logged in</label>
    </div>
  </form>
  <div class="ui bottom attached icon message">
    <i class="signup icon"></i><div class="content">
      <div class="header">New to <i>PrivateAsk</i>?</div>
      <a href="register.php">Register Here</a>
  </div></div>
</div>
</main>

</body>
</html>
