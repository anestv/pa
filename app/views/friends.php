<?php

$friends_json_html = htmlspecialchars(json_encode($data['friends']));

if ($GLOBALS['warnMessage'])
  echo '<div class="ui warning message">'.$GLOBALS['warnMessage'].'</div>';

if ($GLOBALS['friendsSuccess'])
  echo '<div class="center480 ui success message">Your friends have been changed!</div>';
?>
<noscript>
  <div class="ui warning message">
    <div class="header"><i class="warning icon"></i> JavaScript is required for this page</div>
    If Javascript cannot be enabled, add your friends one by one.
    Enter each username in the textbox and submit as many times as needed.
    If you prefer you can edit the JSON representation in the textarea below.
  </div>
  <form method="post">
    <textarea name="friends" cols="60" rows="4"><?=$friends_json_html?></textarea>
    <input type="submit">
  </form>
</noscript>

<div class="ui info message">
  <i class="close icon"></i>
  <div class="header"><i class="info icon"></i> Here you can edit your friends</div>
  <ul class="list">
    <li>To add a friend, enter the exact username in the field, press
      Enter and click the Save button when done. If you don't know the
      exact username, use <a href="search">Search</a> to find it.
    <li>To remove a friend, click the (x) button next to the username.
    <li>You can click at any username to view that profile.
      Please save your friends before doing so.
  </ul>
</div>

<form method="post" id="friendForm">

<input type="hidden" name="friends" value="<?=$friends_json_html?>">
<div class="ui action input">
  <input type="text" id="friendInput" name="friendInput" placeholder="Friend's username" autocomplete="off">
  <div id="addFriend" class="ui icon button"><i class="add icon"></i></div>
</div>

<div class="ui divided relaxed animated link list raised segment">
  <?php 
  foreach ($data['friends'] as $curr) {
    echo '<div class="item"><div class="ui right floated circular icon button">';
    echo '<i class="red remove icon"></i></div><a class="header" href="user/';
    echo $curr .'">'. "$curr</a></div>";
  } ?>
</div>

<button type="submit" class="ui positive centered labeled icon button">
  <i class="save icon"></i>Save friends
</button>
</form>
