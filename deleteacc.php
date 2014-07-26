<?php

if (empty($user))
  terminate('You must be logged in to delete your account', 401);

if ($_SERVER["REQUEST_METHOD"] === "POST"){ //thelei na to diagrapsei
  
  if (empty($_POST["user"]))
    terminate("You did not enter a username", 400);
  if ($_POST["user"] !== $user)
    terminate("You did not enter your account's username", 400);
  
  //TODO password get, hash+Salt, save in var
  
  if (isset($_POST['pass']) and trim($_POST['pass']))
  	$pass = $_POST['pass'];
  else terminate('You did not enter a password', 400);
  
  $userobj = mysqli_fetch_array($con->query("SELECT username , hs_pass FROM users WHERE username = '$user';"));
  
  if (!password_verify($pass, $userobj['hs_pass']))
  	terminate('The password you entered is incorrect');
  

  $query = "UPDATE users SET deleteon = CURRENT_DATE + INTERVAL 7 DAY WHERE username = '$user';"; //TODO where password=...
  $res = $con->query($query);
  
  if (!$res) terminate('A server error occurred', 500);
  else {
    echo "Your account will e deleted in 7 days. You have been logged out";
    session_destroy();
  }
}

?>


<h1>Delete your account</h1>
<p>You are about to delete your account. Your profile and questions you have answered will be erased. 
Questions you have asked anonymously will be kept as they are, but questions you have asked publicly 
(i.e. your name appears in the question) will no longer show your name.</p>
<p>For a 7-day period the information mentioned above will be kept hidden, and will be recoverable by 
logging in your account. After that period, the changes described above will take place and they will 
be IRREVOCABLE and PERMANENT. We will not be able to recover any data after that period.</p>
<p>If you want to procceed, confirm the account's deletion by typing your username and password. </p>

<form method="post">
<input required name="user" type="text" placeholder="Username">
<br>
<input required name="pass" type="password" placeholder="Password">
<br>
<input type="submit" value="Delete">
</form>