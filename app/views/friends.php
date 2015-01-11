<?php

$friends_json_html = htmlspecialchars(json_encode($data['friends']));

if ($GLOBALS['warnMessage'])
  echo '<div class="ui warning message">'.$GLOBALS['warnMessage'].'</div>';

if ($GLOBALS['friendsSuccess'])
  echo '<div class="center480 ui success message">
  <i class="checkmark icon"></i>
  Your friends have been changed!</div>';
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


<main class="center940">
<div class="ui top attached info message">
  <div class="header"><i class="info icon"></i> Edit your friends</div>
  <ul class="list">
    <li>To add a friend, enter the exact username in the field, press
      Enter and click the Save button when done. If you don't know the
      exact username, use <a href="search">Search</a> to find it.
    <li>To remove a friend, click the (x) button next to the username.
    <li>You can click at any username to view that profile.
      Please save your friends before doing so.
    <li>You can also add someone to your friends from their profile.
  </ul>
</div>
<div class="ui bottom attached segment">
<form method="post" class="center480">
  
  <input type="hidden" name="friends" value="<?=$friends_json_html?>">
  <div class="ui fluid left icon right action input">
    <input type="text" id="friendInput" name="friendInput" placeholder="Friend's username" autocomplete="off">
    <i class="users icon"></i>
    <div id="addFriend" class="ui black button" tabindex="0">
      <!-- We should use a button[type=button] so it's focusable,
        but it doesn't display correctly. See github/SemanticUI#793, #1506-->
      Add
    </div>
  </div>
  
  <div class="ui relaxed divided animated list">
    
    <?php
    foreach ($data['friends'] as $curr) {
      echo '<div class="item"><i class="right floated remove red link icon"></i>';
      echo "<a class='header' href='user/$curr'>$curr</a></div>\n";
    } ?>
  </div>
  
  <button type="submit" class="ui centered positive labeled icon button">
    <i class="save icon"></i>Save friends
  </button>
  
</form>
</div>
</main>
