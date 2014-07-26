<html>
<head>
<title>Login - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
</head>
<body>

<?php

if(! empty($_REQUEST['logout'])){ //thelei na aposundethei
  if(session_destroy())
    echo "You have been successfully logged out";
  else terminate("The current session could not be destroyed", 500);

} else if(!empty($user)) { //hdh sundedemenos
  echo "Hello $user, you are already logged in";
  
  //TODO redirect or show message better
  
  
} else if ($_SERVER["REQUEST_METHOD"] === "POST"){ //thelei na sundethei
  
  if (isset($_POST["user"]) and trim($_POST["user"]))
    $user = $con->real_escape_string($_POST["user"]);
  else terminate("You did not specify a username", 400);
  
  if (isset($_POST['pass']) and trim($_POST['pass']))
  	$pass = $_POST['pass'];
  else terminate('You did not enter a password', 400);
  
  
  $user_dbraw = $con->query("SELECT username , hs_pass FROM users WHERE username = '$user';");
  if (! $user_dbraw) terminate("Querying database failed", 500);
  $user_db = mysqli_fetch_array($user_dbraw);
  
  if($user_db['username'] == "")
    terminate('This user does not exist. Maybe you should <a hreg="register.php">register</a>');
  
  if ($user === $user_db['username']){
  	if (!password_verify($pass, $user_db['hs_pass']))
  	  terminate('The password you entered is incorrect');
  	
    //apotroph diagrafhs
    $con->query("UPDATE users SET deleteon = NULL WHERE username = '$user';");
    if (mysqli_affected_rows($con) > 0)
      echo 'Your account has been recovered! Welcome back!';//den tha to diavasei giati tha ginei redirect

    if (!empty($_POST["keep"]))
      session_set_cookie_params(60*60*24*7); //1 evdomada
    session_regenerate_id(true);
    $_SESSION['user'] = $user;
    echo '<meta http-equiv="refresh" content="0;url=index.php">';
  }
  
}

?>

<form method="post" action="?"><!-- to ? einai gia na mhn ksanapaei sto ?logout=1 -->
<input required name="user" type="text" placeholder="Username" autofocus><br>
<input required name="pass" type="password" placeholder="Password"><br>
<input type="checkbox" name="keep" id="keep">
<label for="keep">Stay logged in</label><br>
<input type="submit">
</form>

</body>
</html>
