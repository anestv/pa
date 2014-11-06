<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="application-name" content="PrivateAsk">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.19.3/css/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="<?=helpers\url::get_template_path()?>css/index.css">
  <title>PrivateAsk</title>
</head>
<body>

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

</body>
</html>
