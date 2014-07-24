<html>
<head>
<title>Registration - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
<meta charset="UTF-8">
</head>
<body>

<?php

if(! empty($_SESSION['user'])){
  echo "You are already logged in";
  //TODO maybe redirect to home
} else if ($_SERVER["REQUEST_METHOD"] === "POST"){


  if (!isset($_POST["equation"]) or !isset($_SESSION["equation"])){
    header("HTTP/1.1 418 I'm a teapot");
    die();
  } else if ($_POST["equation"] !== $_SESSION["equation"])
    terminate("Wrong answer, seems you are not good at maths", 418);
  
  if (isset($_POST["username"]) and trim($_POST["username"]))
    $user = mysqli_real_escape_string($con, $_POST["username"]);
  else terminate("A username was not given", 400);

  if (isset($_POST["password"]) and trim($_POST["password"]))
    $pass = $_POST["password"]; //no escaping because it will be hashed anyway
  else terminate("A password was not given", 400);
  
  
  if (isset($_POST["real"]) and trim($_POST["real"]))
  	$realN = mysqli_real_escape_string($con, $_POST["real"]);
  else terminate("You did not enter your real name", 400);
  
  if($user != strip_tags($user))
    terminate("Do not include html special characters in your username", 400);
  if (strlen($pass) > 100)
    terminate('Please enter a password up to 100 characters');
  
  
  $users_raw = mysqli_query($con, "SELECT username FROM users;");
  while($row = mysqli_fetch_array($users_raw)) {
    if ($user === $row['username'])
      terminate('This username already exists', 400);
  }
  if ($user === 'deleteduser' or $user === 'anonymous')
    terminate("Do not use '$user' as a username, as it has a special meaning for the server");
  
  
  //tou kwdikou tou krufou to mperdema kai alatiasma (hash 'n' salt)
  $hexrand = bin2hex(openssl_random_pseudo_bytes(10));
  $alataki = base_convert($hexrand, 16, 30) . $_POST['rand'];
  $cr_arr = array('salt'=> $alataki, 'cost'=> 10);
  $hspass = password_hash($pass, PASSWORD_DEFAULT, $cr_arr);
  $passDB = mysqli_real_escape_string($con, $hspass);
  
  
  $query = "INSERT INTO users(username, hs_pass, realname) VALUES ('$user', '$passDB', '$realN')";
  //$result = mysqli_query($con, $query);
  
  if (!$result)
    terminate("Due to an unknown error your account was not created", 500);
  
  http_response_code(201); //created
  session_regenerate_id(true);
  $_SESSION['user'] = $user; //log him in
  die('<div id="success">
    <span>Success!</span><br><br>
    Your account has been created!<br>
    <a href="/">Home</a>
  </div>
  
  </body>
  </html>');
  
} else {
  $max = mt_rand(1, 10) > 3; //70% pithanothta gia nai
  $expon = mt_rand(1, 9);
  if ($max) $_SESSION['equation'] = strval($expon);
  else $_SESSION['equation'] = '0';
  $ordArr = array('','πρωτο','δευτερο','τριτο','τεταρτο','πεμπτο','εξα','εφτα','οχτα','εννια');
  $verifQ = 'Πόσες '. ($max? 'το πολύ':'τουλάχιστον').' λύσεις έχει μια '.$ordArr[$expon] .'βάθμια εξίσωση; ';
}

?>

<form method="post" autocomplete="off" accept-charset="ASCII">
<input type="text" maxlength="20" required name="username" placeholder="Username" pattern="\w{5,20}">
<img src="res/info.svg" height="20" title="English letters and numbers allowed, 5-20 characters">
<br>
<input type="password" maxlength="101" required name="password" placeholder="Password" pattern=".{6,}">
<img src="res/info.svg" height="20" title="6-100 characters">
<br>
<input type="text" required maxlength="40" name="real" placeholder="Real Name">
<img src="res/info.svg" height="20" title="Up to 40 characters">
<br><br>
<?=$verifQ?><input type="number" min="0" max="9" name="equation" required><br>

<br>Enter here anything you want<br>
<input type="text" maxlength="50" name="rand" placeholder="randomness" required><br>

<p>By procceeding, you agree to the <a href="terms.html">Terms and Conditions</a></p>

<input type="submit">
</form>


</body>
</html>