<!DOCTYPE html>
<html>
<head>
  <title>Change Password - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <link rel="stylesheet" type="text/css" href="css/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="css/register.css"><!-- for the orange header -->
  <meta charset="utf-8">
</head>
<body>
<main class="center940">
<?php

if (!$user)
  terminate('You must be logged in to change your password <br><a href="login.php">Log in</a>', 401);


if ($_SERVER["REQUEST_METHOD"] === "POST"){


if (isset($_POST["old"]) and trim($_POST["old"]))
  $old = $_POST["old"]; //no escaping because it will be hashed anyway
else terminate("The old password was not given", 400);

if (isset($_POST["new"]) and isset($_POST['new2']) and trim($_POST["new"]))
  $new = $_POST["new"];
else terminate("A new password was not given", 400);

if (strlen($new) < 6)
  terminate('Please enter a password of more than 6 characters');
if (strlen($new) > 100)
  terminate('Please enter a password up to 100 characters');

if ($new !== $_POST['new2'])
  terminate("The new passwords do not match");


$r1 = $con->query("SELECT hs_pass FROM users WHERE username = '$user';");
if (!$r1) terminate('Server error:'.$con->error, 500);

if (!password_verify($old, $r1->fetch_array()['hs_pass']))
  terminate('The old password is incorrect');

if ($old === $new)
  terminate("The password is the same as before");

//tou kwdikou tou krufou to mperdema kai alatiasma (hash 'n' salt)
$hexrand = bin2hex(openssl_random_pseudo_bytes(10));
$thirand = base_convert($hexrand, 16, 30);
$alataki = $thirand. $_POST['rand']. $thirand; //an to post.rand einai poly mikro
$cr_arr = array('salt'=> $alataki, 'cost'=> 10);
$hspass = password_hash($new, PASSWORD_DEFAULT, $cr_arr);
$passDB = $con->real_escape_string($hspass);

if (!$passDB) terminate('Fatal server error: password for insertion is empty');

$res = $con->query("UPDATE users SET hs_pass = '$passDB' WHERE username = '$user';");

echo '<div class="ui message ';
if ($res) echo 'success"><i class="checkmark icon"></i>Your password was successfully changed!';
else echo 'error"><i class="attention icon"></i>Your password was NOT changed. '. $con->error;
echo '</div>';
}
?>

<h1 class="ui top attached center aligned block inverted header">
  <a href="./"><i class="home link icon"></i></a>
  Change your password
</h1>
<div class="ui attached info message">
  <div class="header">
    We're glad to see you want to change your password!
  </div>
  Reminder: passwords must be 6-100 characters and should be difficult to guess.
</div>
<form method="post" class="ui bottom attached form segment" autocomplete="off">
  <div class="ui two column stackable grid">
    <div class="column">
      <div class="field">
        <input type="password" required name="old" placeholder="Old password">
      </div>
      <div class="field">
        <input type="text" required name="rand" placeholder="Enter something random" maxlength="20">
      </div>
    </div>
    <div class="column">
      <div class="field">
        <input type="password" required name="new" placeholder="New password" maxlength="101" pattern=".{6,}">
      </div>
      <div class="field">
        <input type="password" required name="new2" placeholder="The new one again">
      </div>
    </div>
  </div>
  <button type="submit" class="ui centered positive animated fade button">
    <div class="hidden content"><i class="save icon"></i></div>
    <div class="visible content">Save</div>
  </button>
</form>
</main>
</body>
</html>
