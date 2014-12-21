<?php
if ($data['noOld']){
  $action = 'set a password';
  $input = 'disabled';
} else {
  $action = 'change your password';
  $input = 'required';
}
?>
<main class="center940">
<h1 class="ui top attached center aligned block orange inverted header">
  <a href="./"><i class="home link icon"></i></a>
  <?=ucfirst($action)/* capitalise first letter*/ ?>
</h1>
<div class="ui attached info icon message">
  <i class="info icon"></i>
  <div class="content">
    <div class="header">
      We're glad to see you want to <?=$action?>!
    </div>
    Reminder: passwords must be 6-100 characters and should be difficult to guess.
  </div>
</div>
<form method="post" class="ui bottom attached form segment <?php if ($GLOBALS['warnMessage']) echo 'error';?>" autocomplete="off">
  <div class="ui error message">
    <?=$GLOBALS['warnMessage']?>
  </div>
  <?php if ($GLOBALS['changePassSuccess'])
    echo '<div class="ui success message">Your password has been changed!</div>';?>
  <div class="ui two column stackable grid">
    <div class="column">
      <div class="field">
        <input type="password" <?=$input?> name="old" placeholder="Old password">
      </div>
      <div class="field">
        <input type="text" required name="rand" placeholder="Enter something random" maxlength="20">
      </div>
    </div>
    <div class="column">
      <div class="field">
        <input type="password" required name="new" placeholder="New password" maxlength="101" pattern=".{6,}">
      </div>
      <div class="field">
        <input type="password" required name="new2" placeholder="The new one again">
      </div>
    </div>
  </div>
  <button type="submit" class="ui centered positive animated fade button">
    <div class="hidden content"><i class="save icon"></i></div>
    <div class="visible content">Save</div>
  </button>
</form>
</main>