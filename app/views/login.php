<main class="center940">
<?php

if ($_SESSION['requiredLogin'])
  echo '<div class="ui warning message"><h2 class="header">'.
  '<i class="warning icon"></i> Login required</h2>'.
  'In order to view this page, please log in</div>';
// do not unset $_SESSION['requiredLogin'] so that
// we can redirect there in controllers\login@post

if ($_SESSION['loggedOut']){
  unset($_SESSION['loggedOut']);
  echo '<div class="ui info message"><h2 class="header">'.
  '<i class="sign out icon"></i> You have been logged out</h2>'.
  'We hope to see you soon!</div>';
}

if ($_SESSION['userNotFound']){
  echo '<div class="ui warning message"><h2 class="header">'.
  '<i class="warning icon"></i>Please log in again</h2>'.
  $_SESSION['userNotFound'].'</div>';
  unset($_SESSION['userNotFound']);
}
?>

<div class="center480">
  <h3 class="ui top attached header">Log in to <i>PrivateAsk</i></h3>
  <form action="login" method="POST" class="ui attached form segment <?php if ($GLOBALS['warnMessage']) echo 'error';?>">
    <div class="ui error message">
      <?=$GLOBALS['warnMessage']?>
    </div>
    <div class="ui left icon input field">
      <input name="user" required type="text" placeholder="Username" autofocus>
      <i class="user icon"></i>
    </div>
    <div class="ui left icon input field">
      <input name="pass" required type="password" placeholder="Password">
      <i class="lock icon"></i>
    </div>
    <div class="ui toggle checkbox">
      <input type="checkbox" name="keep" id="keep">
      <label for="keep">Keep me logged in</label>
    </div>
    <button type="submit" class="ui right floated right labeled icon positive button">
      Log in<i class="sign in icon"></i>
    </button>
  </form>
  <div class="ui attached segment">
    <a href="<?=$data['fbLoginUrl']?>" class="ui facebook fluid button">
      <i class="facebook icon"></i>Login with Facebook
    </a>
  </div>
  <div class="ui bottom attached icon message">
    <i class="signup icon"></i>
    <div class="content">
      <div class="header">New to <i>PrivateAsk</i>?</div>
      <a href="register">Register Here</a>
    </div>
  </div>
</div>

</main>
