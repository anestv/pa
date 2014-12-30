<?php
if ($_SESSION['alreadyLoggedIn']){
  unset($_SESSION['alreadyLoggedIn']);
  echo '<div class="ui info message">Hello '.$data['username'].', you are already logged in</div>';
}

if ($_SESSION['registerSuccess']){
  unset($_SESSION['registerSuccess']);
  echo '<div class="ui success message"><i class="checkmark icon">'.
    '</i>Your account has been created and you are logged in!</div>';
}
?>
