<nav class="ui seven item inverted fluid menu">
  <img class="item" src="res/logo.svg" alt="PrivateAsk logo" height="50">
  <a class="item" href="user/<?=$data['user']->username?>"><i class="user icon"></i>Your profile</a>
  <a class="item" href="search"><i class="search icon"></i> Search</a>
  <a class="item" href="pending"><i class="question icon"></i>
    Pending Qs <?php
    if ($data['unseen'])
      echo ' <div class="ui red label" id="unansweredCount">'.$data['unseen'] .'</div>';
  ?></a>
  <a class="item" href="settings"><i class="setting icon"></i>Settings</a>
  <a class="item" href="help" rel="help"><i class="help icon"></i>Help - FAQ</a>
  <a class="item" href="logout"><i class="sign out icon"></i>Log out</a>
</nav>
