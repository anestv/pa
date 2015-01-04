<?php 
$page = \core\Router::$requestedRelPath;
$profileUrl = 'user/' . $GLOBALS['user']->username;

if ($GLOBALS['user']->isRealUser()): ?>

<nav class="ui orange inverted fluid menu">
  <a class="item <?=$page == ''?'active':''?>" href=".">PrivateAsk</a>
  <a class="item <?=$page == $profileUrl?'active':''?>" href="<?=$profileUrl?>"><i class="user icon"></i>Your profile</a>
  <a class="item <?=$page == 'search'?'active':''?>" href="search" rel="search"><i class="search icon"></i> Search</a>
  <a class="item <?=$page == 'pending'?'active':''?>" href="pending"><i class="question icon"></i>
    Pending Qs <?php
    $unseen = $GLOBALS['user']->getUnseen();// TODO move it somewhere else
    if ($unseen)
      echo '<div class="ui red label" id="unansweredCount">'.$unseen .'</div>';
  ?></a>
  <span class="right menu">
    <a class="item <?=$page == 'settings'?'active':''?>" href="settings"><i class="setting icon"></i>Settings</a>
    <a class="item <?=$page == 'help'?'active':''?>" href="help" rel="help"><i class="help icon"></i>Help - FAQ</a>
    <a class="item" href="logout"><i class="sign out icon"></i>Log out</a>
  </span>
</nav>
<?php else: ?>

<nav class="ui orange inverted fluid menu">
  <a class="item <?=$page == ''?'active':''?>" href=".">PrivateAsk</a>
  <a class="item <?=$page == 'search'?'active':''?>" href="search" rel="search"><i class="search icon"></i> Search</a>
  <span class="right menu">
    <a class="item <?=$page == 'help'?'active':''?>" href="help" rel="help"><i class="help icon"></i>Help - FAQ</a>
    <a class="item <?=$page == 'login'?'active':''?>" href="login"><i class="sign out icon"></i>Log in</a>
  </span>
</nav>
<?php endif ?>
