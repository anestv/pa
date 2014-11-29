<nav class="ui seven item inverted fluid menu">
  <a class="item" href=".">PrivateAsk</a>
  <a class="item" href="user/<?=$data['username']?>"><i class="user icon"></i>Your profile</a>
  <a class="item" href="search"><i class="search icon"></i> Search</a>
  <a class="item" href="pending"><i class="question icon"></i>
    Pending Qs <?php
    if ($data['unseen'])
      echo '<div class="ui red label" id="unansweredCount">'.$data['unseen'] .'</div>';
  ?></a>
  <a class="item" href="settings"><i class="setting icon"></i>Settings</a>
  <a class="item" href="help" rel="help"><i class="help icon"></i>Help - FAQ</a>
  <a class="item" href="logout"><i class="sign out icon"></i>Log out</a>
</nav>

<?php
if ($_SESSION['alreadyLoggedIn']){
  echo '<div class="ui info message">Hello '.$data['username'] .', you are already logged in</div>';
  unset($_SESSION['alreadyLoggedIn']);
}
?>
