<?php

$fonts = ["Aliquam","Arial","Calibri","Cambria","Comfortaa","Comic Sans MS","Courier",
"Garamond","Josefin Sans","Leander","Segoe UI","Tahoma","Times New Roman","Trench","Verdana"];
$privacyVals = ['friends', 'users', 'all'];
$privacyOpts = ['Only your friends', 'Registered users' ,'Everyone'];

function writeDropdown($name, $values, $strings = null){
  $user = $GLOBALS['user']->raw;
  if ($strings === null) $strings = $values;
  ?>
  <div class="ui selection dropdown scriptOnly">
    <!-- dropdown initialized from hidden input's value-->
    <input type="hidden" name="<?=$name?>" value="<?=$user[$name]?>">
    <div class="text"> </div>
    <i class="dropdown icon"></i>
    <div class="menu">
      <?php
      for ($i = 0; $i < count($values); $i++){
        $state = ($user[$name] === $values[$i])? 'active ':'';
        echo '<div class="'. $state .'item" data-value="';
        echo $values[$i] .'">'. $strings[$i] .'</div>';
      }
  echo '</div></div>';
  
  // noscript MUST be after the hidden input
  // so that the hidden input's value is overwritten
  echo '<noscript><select required name="'.$name.'">';
  echo '<option value="">Select one</option>'; //required for valid html5
  for ($i = 0; $i < count($values); $i++){
    $state = ($user[$name] === $values[$i])? ' selected' :'';
    echo '<option value="'.$values[$i]."\"$state>".$strings[$i].'</option>';
  }
  echo '</select></noscript>';
}
?>

<h1 class="ui top attached center aligned orange inverted block header">
  <a href="."><i class="home link icon"></i></a>Settings
</h1>
<form method="post" class="ui bottom attached form segment<?php if ($GLOBALS['warnMessage']) echo ' warning'?>">
  <div class="ui warning message">
    Hmm, there were some problems:
    <ul><?=$GLOBALS['warnMessage']?></ul>
  </div>
  
  <div class="ui three column doubling grid">
    <div class="column">
      <h3 class="ui header">Account</h3>
      <div class="field">
        <label>Real Name:</label>
        <input type="text" name="realname" required value="<?=$data['u']['realname']?>" placeholder="Your real name" maxlength="40">
      </div>
      <div class="ui vertical fluid menu">
        <a href="friends" class="item"><i class="users icon"></i>Your Friends</a>
<?php
// dont use $data['u'] to be able to access connectedFb 
if ($GLOBALS['user']->hs_pass == '-')
  echo '<a href="changepass" class="item"><i class="lock icon"></i>Set a password</a>';
else {
  echo '<a href="changepass" class="item"><i class="lock icon"></i>Change your password</a>';
  
  if ($GLOBALS['user']->connectedFb)
    echo '<a href="api/disconnectFacebook" class="item"><i class="facebook icon"></i>Disonnect from Facebook</a>';
  else echo '<a href="api/connectFacebook" class="item"><i class="facebook icon"></i>Connect with Facebook</a>';
} ?>
        <a href="deleteaccount" class="red item"><i class="off icon"></i>Delete your Account</a>
      </div>
    </div>
    <div class="column">
      <h3 class="ui header">Privacy</h3>
      <div class="field">
        <label>Who can view your profile?</label>
        <?=writeDropdown('whosees',$privacyVals,$privacyOpts)?>
      </div>
      <div class="field">
        <label>Who can ask you a question?</label>
        <?=writeDropdown('whoasks',$privacyVals,$privacyOpts)?>
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
        <input type="color" name="backcolor" required value="<?=$data['u']['backcolor']?>">
      </div>
      <div class="inline field">
        <label>Header color:</label>
        <input type="color" name="headcolor" required value="<?=$data['u']['headcolor']?>">
      </div>
      <div class="inline field">
        <label>Text font family:</label>
        <?=writeDropdown('textfont',$fonts)?>
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
