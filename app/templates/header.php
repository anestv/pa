<!DOCTYPE html>
<!-- PrivateAsk - Copyright Anestis Varsamidis 2014 -
http://github.com/anestv/pa - Open source: Artistic License 2.0 -->
<html>
<head>
  <meta charset="utf-8">
  <title><?php if (isset($data['title']))echo $data['title'].' - '; echo SITETITLE; //SITETITLE defined in config.php?></title>
  
  <?php
  echo '<base href="'. BASE_DIR .'">';
  
  echo '<link rel="stylesheet" type="text/css" href="resources/css/general.css">';
  
  //TODO replace with our build
  echo '<link rel="stylesheet" href="https://pa-anestv-1.c9.io/node_modules/semantic-ui/dist/semantic.min.css">';
  
  if (isset($data['styles']) and is_array($data['styles']))
    foreach($data['styles'] as $style)
      if ($style[0] == '/')
        echo '<link rel="stylesheet" type="text/css" href="'.substr($style, 1).'">'; // remove first slash
      else
        echo "<link rel='stylesheet' type='text/css' href='resources/css/$style'>";
?>
</head>
<body <?=$data['bodyData']?>>

<div id="outWrapper">
<!-- wrapper required for sticky footer bar -->

<?php // TOP BAR

$page = \core\Router::$requestedRelPath;
$profileUrl = 'user/' . $GLOBALS['user']->username;

if ($GLOBALS['user']->isRealUser()): ?>

<nav class="ui orange inverted fluid menu">
  <a class="item <?=$page == ''?'active':''?>" href=".">PrivateAsk</a>
  <a class="item <?=$page == $profileUrl?'active':''?>" href="<?=$profileUrl?>"><i class="user icon"></i>Your profile</a>
  <a class="item <?=$page == 'search'?'active':''?>" href="search" rel="search"><i class="search icon"></i> Search</a>
  <a class="item <?=$page == 'pending'?'active':''?>" href="pending"><i class="question icon"></i>
    Pending Qs <?php
    $unseen = $GLOBALS['user']->getUnseen();
    if ($unseen) echo "<div class='ui red label' id='unansweredCount'>$unseen</div>";
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
