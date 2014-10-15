<!DOCTYPE html>
<html>
<head>
  <title>Settings - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <link rel="stylesheet" type="text/css" href="res/fonts/customFonts.css">
  <link rel="stylesheet" type="text/css" href="css/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="css/profileAppearance.dcss.php">
  <meta charset="UTF-8">
  <style>
    body {padding: 1em 4rem 0;}
    .ui.form.segment {padding-top: 0;}
    #displayPreview span {
      display: inline-block;
      padding: 0.5em 1.5em;
      line-height: 1.6;
    }
    input[type="color"]{
      padding: 0 2px!important;
      min-height: 2em;
      min-width: 4em;
      border-width: 0!important;
    }
    a.red.item {
      color: red !important;
      font-weight: bold;
    }
  </style>
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

$privacyVals = array('friends', 'users', 'all');
$privacyOpts = array('Only your friends', 'Registered users' ,'Everyone');

if ($_SERVER["REQUEST_METHOD"] === "POST"){
  
  if (!(isset($_POST['whosees']) and isset($_POST['whoasks']) and 
      in_array($_POST['whosees'],$privacyVals)and in_array($_POST['whoasks'],$privacyVals)))
    terminate('Choose one of the shown privacy settings',400);
  
  if (isset($_POST['real']) and trim($_POST['real']))
    $real= $con->real_escape_string(htmlspecialchars($_POST['real']));
  else terminate ("Please enter your real name", 400);
  
  if (isset($_POST['fontfamily']) and in_array($_POST['fontfamily'],$fonts))
    $fontf = $_POST['fontfamily'];
  else terminate ("Please select a font family", 400);

  if (empty($_POST['bcolor']) or empty($_POST['hcolor']))
    terminate('Select a background and text color',400);
  if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',$_POST['bcolor'])!==1 or
      preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',$_POST['hcolor'])!==1)
    terminate('Select a background and text color');
  $bcolor = $_POST['bcolor'];
  $hcolor = $_POST['hcolor'];

  $see = $_POST['whosees'];
  $ask = $_POST['whoasks'];

  $query = "UPDATE users SET realname = '$real', whosees = '$see', ".
      "whoasks = '$ask', backcolor = '$bcolor', headcolor = '$hcolor', ".
      "textfont = '$fontf' WHERE username = '$user';";
  
  $result = $con->query($query);
  
  if ($result)
    echo '<div class="ui success message"><div class="header">'.
    '<i class="checkmark icon"></i>Your settings have been changed</div></div>';
  else
    echo '<div class="ui error message"><div class="header"><i '.
    'class="attention icon"></i>Your settings were not changed '.$con->error.'</div></div>';
  
  //xreiazontai gia na fainontai swsta ta
  //ta selected options meta apo update
  $settings['whosees'] = $see;
  $settings['whoasks'] = $ask;
  $settings['realname']= $real;
  $settings['backcolor'] = $bcolor;
  $settings['headcolor'] = $hcolor;
  $settings['textfont'] = $fontf;
}

function writeDropdown($inputName, $dbName, $values, $strings = null){
  global $settings;
  if ($strings === null) $strings = $values;
  ?>
  <div class="ui selection dropdown scriptOnly">
    <!-- dropdown initialized from hidden input's value-->
    <input type="hidden" name="<?=$inputName?>" value="<?=$settings[$dbName]?>">
    <div class="text"> </div>
    <i class="dropdown icon"></i>
    <div class="menu">
      <?php
      for ($i = 0; $i < count($values); $i++){
        $state = ($settings[$dbName] === $values[$i])? 'active ':'';
        echo '<div class="'. $state .'item" data-value="';
        echo $values[$i] .'">'. $strings[$i] .'</div>';
      }
  echo '</div></div>';
  
  // noscript MUST be after the hidden input
  // so that the hidden input's value is overwritten
  echo '<noscript><select required name="'.$inputName.'">';
  echo '<option value="">Select one</option>'; //required for valid html5
  for ($i = 0; $i < count($values); $i++){
    $state = ($settings[$dbName] === $values[$i])? ' selected' :'';
    echo '<option value="'.$values[$i]."\"$state>".$strings[$i].'</option>';
  }
  echo '</select></noscript>';
}
?>

<h1 class="ui top attached center aligned orange inverted block header">
  <a href="."><i class="home link icon"></i></a>Settings
</h1>
<form method="post" class="ui bottom attached form segment">
  <div class="ui three column doubling grid">
    <div class="column">
      <h3 class="ui header">Account</h3>
      <div class="field">
        <label>Real Name:</label>
        <input type="text" name="real" required value="<?=$settings['realname']?>" placeholder="Your real name" maxlength="40">
      </div>
      <div class="ui vertical fluid menu">
        <a href="friends.php" class="item"><i class="users icon"></i>Your Friends</a>
        <a href="changepass.php" class="item"><i class="lock icon"></i>Change password</a>
        <a href="deleteacc.php" class="red item"><i class="off icon"></i>Delete your Account</a>
      </div>
    </div>
    <div class="column">
      <h3 class="ui header">Privacy</h3>
      <div class="field">
        <label>Who can view your profile?</label>
        <?=writeDropdown('whosees','whosees',$privacyVals,$privacyOpts)?>
      </div>
      <div class="field">
        <label>Who can ask you a question?</label>
        <?=writeDropdown('whoasks','whoasks',$privacyVals,$privacyOpts)?>
      </div>
    </div>
    <div class="column" id="displaySettings">
      <h3 class="ui header">Display</h3>
      <p>
        These settings will be used to display your profile.<br>
        <a href="http://www.colourlovers.com/colors">Need inspiration?</a>
        <i class="external url teal icon"></i>
      </p>
      <div class="inline field">
        <label>Background color:</label>
        <input type="color" name="bcolor" required value="<?=$settings['backcolor']?>">
      </div>
      <div class="inline field">
        <label>Header color:</label>
        <input type="color" name="hcolor" required value="<?=$settings['headcolor']?>">
      </div>
      <div class="inline field">
        <label>Text font family:</label>
        <?=writeDropdown('fontfamily','textfont',$fonts)?>
      </div>
      
      <noscript>Unless you enable Javascript the preview below will not function correctly.</noscript>
      <div id="displayPreview" class="ui segment">
        <span class="header">
          <big><b>This</b> is a <i>preview</i></big>, <b>Hello</b> world!
        </span>
      </div>
      
    </div>
  </div>
  
  <button type="submit" class="ui animated positive centered button">
    <div class="visible content">Save</div>
    <div class="hidden content"><i class="save icon"></i></div>
  </button>
  
</form>

<script src="js/jquery2.min.js"></script>
<script src="js/semantic.min.js"></script>
<script src="js/settings.js"></script>

</body>
</html>
