<!DOCTYPE html>
<html>
<head>
  <title>Registration - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.19.3/css/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <link rel="stylesheet" type="text/css" href="css/register.css">
  <meta charset="UTF-8">
</head>
<body>
<main class="center940">

<?php
if ($user){
  redirect('index.php');
?>
  <div class="ui info message">
    <div class="header">
      <i class="info icon"></i>Hello <?=$user?>, you are already logged in
    </div>
    Go to the <a href=".">Home page</a> &emsp; <a href="logout.php">Log out</a>
  </div>
  </main>
  </body>
  </html>
  <?php
  die;
} else if (!empty($_POST["ToS"])){
  
  if (!empty($_POST["datebirth"])){
    header("HTTP/1.1 418 I'm a teapot");
    session_write_close(); //let other requests continue
    sleep(70);
    die();
  }
  
  try {
    if (isset($_POST["username"]) and trim($_POST["username"]))
      $user = $con->real_escape_string($_POST["username"]);
    else throw new Exception("A username was not given");
    
    if (isset($_POST["password"]) and trim($_POST["password"]))
      $pass = $_POST["password"]; //no escaping because it will be hashed anyway
    else throw new Exception("A password was not given");
    
    if (isset($_POST["real"]) and trim($_POST["real"]))
      $realN = $con->real_escape_string($_POST["real"]);
    else throw new Exception("You did not enter your real name");
    
    
    if (preg_match('/^\w{5,20}$/', $user) !== 1)
      throw new Exception('Enter 5-20 English letters and numbers as username');
    if (strlen($pass) < 6)
      throw new Exception('Please enter a password of more than 6 characters');
    if (strlen($pass) > 100)
      throw new Exception('Please enter a password up to 100 characters');
    
    
    $user_exist_res = $con->query("SELECT username FROM users WHERE username = '$user';");
    if (!$user_exist_res or $user_exist_res->num_rows !== 0)
      throw new Exception('This username already exists');
    
    if ($user === 'deleteduser' or $user === 'anonymous')
      throw new Exception("Do not use '$user' as a username, as it has a special meaning for the server");
    
    
    //tou kwdikou tou krufou to mperdema kai alatiasma (hash 'n' salt)
    $hexrand = bin2hex(openssl_random_pseudo_bytes(10));
    $thirand = base_convert($hexrand, 16, 30);
    $alataki = $thirand. $_POST['rand']. $thirand;
    $cr_arr = array('salt'=> $alataki, 'cost'=> 10);
    $hspass = password_hash($pass, PASSWORD_DEFAULT, $cr_arr);
    $passDB = $con->real_escape_string($hspass);
    
    
    $query = "INSERT INTO users(username, hs_pass, realname) VALUES ('$user', '$passDB', '$realN')";
    $result = $con->query($query);
    
    if (!$result)
      throw new RuntimeException($con->error);
    
    redirect("user/$user", 201); //won't redirect, just set Location, http created
    session_regenerate_id(true);
    $_SESSION['user'] = $user; //log him in
    ?>

<div class="aloneInPage ui success icon message">
  <i class="checkmark icon"></i><div class="content">
  <h2 class="header">Success!</h2>
  Your account has been created and you are logged in!<br>
  <a href=".">Home</a> &emsp; <a href="user/<?=$user?>">Your profile</a>
</div></div>
</main>
</body>
</html>
    <?php 
    die;
    
  } catch (Exception $e) {
    handleException($e);
    //no need to call some other function or something
    //the registration form will be printed anyway
  }
}
?>
<h1 class="ui center aligned orange inverted block header top attached">
 <a href="./"><i class="home link icon"></i></a>
 Create an account on PrivateAsk
</h1>

<form method="post" autocomplete="off" class="ui form bottom attached segment">
<div class="ui two column stackable grid">
 
 <div class="column">
  <div class="field">
   <div class="ui pointing below label">Username: 5 - 20 English letters and numbers</div>
   <input type="text" maxlength="20" required name="username" placeholder="Username" pattern="\w{5,20}">
  </div>
  <div class="field">
   <div class="ui pointing below label">Real Name: Up to 40 characters</div>
   <input type="text" required maxlength="40" name="real" placeholder="Real Name">
  </div>
 </div>
 
 <div class="column">
  <div class="field">
   <div class="ui pointing below label">Password: 6 - 100 characters</div>
   <input type="password" maxlength="101" required name="password" placeholder="Password" pattern=".{6,}">
  </div>
  <div class="field">
   <div class="ui pointing below label">Enter something random</div>
   <input type="text" maxlength="50" name="rand" placeholder="randomness" required>
  </div>
  <div class="inline field">
   <div class="ui checkbox">
    <input type="checkbox" required id="ToScheck" name="ToS">
    <label for="ToScheck">I agree to the <a href="terms.html">Terms and Conditions</a></label>
   </div>
  </div>
 </div>
</div>

<div id="mperdevoBots"><!-- kind of honeypot trap for bots -->
 <input type="text" name="datebirth" maxlength="20" placeholder="You shall not  fill this field">
</div>

<button type="submit" class="ui animated fade centered positive button">
 <div class="visible content">Register</div>
 <div class="hidden content"><i class="signup icon"></i></div>
</button>

</form>
</main>
</body>
</html>
