<html>
<head>
<title>Settings - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
<meta charset="UTF-8">
</head>
<body>

<?php
if (!$user)
  terminate('You must be logged in to change your settings <br><a href="login.php">Log in</a>', 401);

$settings = $con->query("SELECT * FROM users WHERE username = '$user';")->fetch_array();

if (empty($settings['username']))
  terminate("Your accont does not seem to exist.", 403);

$fonts = array("Aliquam","Arial","Calibri","Cambria","Comfortaa","Comic Sans MS","Courier",
"Garamond","Josefin Sans","Leander","Segoe UI","Tahoma","Times New Roman","Trench","Verdana");

if ($_SERVER["REQUEST_METHOD"] === "POST"){
  $possvals = array('friends', 'users', 'all');
  if (!(isset($_POST['whosees']) and isset($_POST['whoasks']) and 
      in_array($_POST['whosees'],$possvals)and in_array($_POST['whoasks'],$possvals)))
    terminate('Choose one of the shown privacy settings',400);
  
  if (isset($_POST['real']) and trim($_POST['real']))
    $real= $con->real_escape_string(htmlspecialchars($_POST['real']));
  else terminate ("Please enter your real name", 400);

  if (isset($_POST['fontfamily']) and in_array($_POST['fontfamily'],$fonts))
    $fontf = $_POST['fontfamily'];
  else terminate ("Please select a font family", 400);

  if (empty($_POST['bcolor']) or empty($_POST['tcolor']))
    terminate('Select a background and text color',400);
  if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',$_POST['bcolor'])!==1 or
      preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',$_POST['tcolor'])!==1)
    terminate('Select a background and text color');
  $bcolor = $_POST['bcolor'];
  $tcolor = $_POST['tcolor'];

  $see = $_POST['whosees'];
  $ask = $_POST['whoasks'];

  $query = "UPDATE users SET realname = '$real', whosees = '$see', ".
      "whoasks = '$ask', backcolor = '$bcolor', textcolor = '$tcolor', ".
      "textfont = '$fontf' WHERE username = '$user';";
  
  $result = $con->query($query);
  
  if ($result) echo "Your settings have been changed!"; //TODO sth better
  else echo "Your settings were not changed". $con->error;
  
  //xreiazontai gia na fainontai swsta ta
  //ta selected options meta apo update
  $settings['whosees'] = $see;
  $settings['whoasks'] = $ask;
  $settings['realname']= $real;
  $settings['backcolor'] = $bcolor;
  $settings['textcolor'] = $tcolor;
  $settings['textfont'] = $fontf;
}

function getLists($prop){
  global $settings;
  $res = '<select name="'.$prop .'"> <option value="friends"';
  if ($settings[$prop]=== 'friends') $res .= ' selected';
  $res .= '>Only your friends</option> <option value="users"';
  if ($settings[$prop]=== 'users') $res .= ' selected';
  $res .= '>Registered users</option> <option value="all"';
  if ($settings[$prop]=== 'all') $res .= ' selected';
  $res .= '>Everyone</option> </select>';
  return $res;
}

?>

<h1>Settings</h1>

<form method="post">
<label for="real">Your real name: </label>
<input type="text" name="real" required value="<?=$settings['realname']?>"
 placeholder="Your real name" maxlength="40" id="real">
<br>

<h2>Display settings</h2>
These settings will be used to display your profile.<br>
Background color: <input type="color" name="bcolor" required value="<?=$settings['backcolor']?>"><br>
Text color: <input type="color" name="tcolor" required value="<?=$settings['textcolor']?>"><br>
Text font family: <select name="fontfamily" required>
 <?php
  foreach($fonts as $curr)
    echo '<option'.($settings['textfont']===$curr?' selected':'').">$curr</option>";
 ?>
</select>

<h2>Privacy settings</h2>

Who can see your profile and questions you answered?
<?=getLists('whosees')?>
<br>
Who can ask you questions?
<?=getLists('whoasks')?>

<br><a href="friends.php">Manage your friends</a><br>
<a href="changepass.php">Change your password</a><br>
<a href="deleteacc.php" class="red">DELETE your account</a>

<br><br>
<input type="submit">
</form>

</body>
</html>