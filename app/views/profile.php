<header id="profileHeader">
  <div><i class="user big circular icon"></i><h1><?=$data['owner']->realname?></h1></div>
  <div>
<?php
use \controllers\Profile as Profile;

if ($data['friendBut'] == Profile::FRIEND_REMOVE_BUTTON)
  echo '<a class="ui active toggle labeled right floated left icon button" '.
    'href="friends"><i class="user icon"></i><span>Friend</span></a>';
else if ($data['friendBut'] == Profile::FRIEND_ADD_BUTTON)
  echo '<a class="ui toggle labeled right floated left icon button" '.
    'href="friends"><i class="user icon"></i><span>Add friend</span></a>';
?>
    <span>Username: <?=$data['owner']->username?></span>
  </div>
</header>
<?php

function successMsg($text){
  echo '<div class="center480 ui success message"><i class="checkmark icon">'.
    "</i>$text<i class=\"close icon\"></i></div>";
}

if ($_SESSION['reportSuccess']){
  unset($_SESSION['reportSuccess']);
  successMsg('Thank you for reporting this question!');
}

if ($_SESSION['deleteSuccess']){
  unset($_SESSION['deleteSuccess']);
  successMsg('Your question has been deleted');
}

if ($_SESSION['questionSent']){
  unset($_SESSION['questionSent']);
  successMsg('Your question has been submitted');
}

if ($data['ask'] == Profile::TRY_LOGIN)
  echo '<div class="ui large warning message"><i class="warning icon">'.
    '</i>You must <a href="login">log in</a> to ask a question</div>';
else if ($data['ask'] != Profile::ABLE)
  echo '<div class="ui large warning message"><i class="warning icon">'.
    '</i>Sorry, you do not have the right to ask a question</div>';
else {
?>
<form method="post" class="ask ui dimmable form segment" action="api/ask">

  <div class="ui dimmer"><div class="content">
    <!-- .center is for semantic modal behavior to close on click on dimmer-->
    <div class="center ui icon header">
      <i class="green checkmark icon"></i>
      <p>Question submitted!</p>
      <div class="ui blue small button">Ask another one?</div>
    </div>
  </div></div>
  <input type="hidden" name="to" value="<?=$data['owner']->username?>">
  <textarea name="question" placeholder="Ask a question" required maxlength="200"></textarea>
  <div id="askControls">
<?php
  if ($data['pubask'] == Profile::PUBASK_ALWAYS)
    echo "<i>Others will <u>see that you asked</u> this question</i>";
  else if ($data['pubask'] == Profile::PUBASK_CHOOSE)
    echo '<label for="publicaskcheckbox"><input type="checkbox" name="pubAsk"
    id="publicaskcheckbox">Show that I asked this question</label>';
?>
  <button type="submit" class="ui blue small icon animated button">
    <div class="hidden content"><i class="question icon"></i></div>
    <div class="visible content">Ask</div>
  </button>
  </div>
</form>
<?php }
echo '<div id="qContainer">';

if ($data['see'] == Profile::TRY_LOGIN)
  echo '<div class="ui large warning message"><i class="warning icon">'.
    '</i><a href="login">Log in</a> to view this user\'s questions</div>';
else if ($data['see'] == Profile::ABLE) {
  $num = count($data['questions']);
  
  if ($num == 0){
    echo '<div class="center480 ui info message"><i class="info icon"></i>';
    echo 'There are no questions to show</div>';
  } else if ($num > 10)
    array_pop($data['questions']); // remove last element
  
  foreach ($data['questions'] as $q)
    $q->writeOut();
}

echo '</div>';

if (isset($num)){
  
  if ($num > 10)
    echo '<button id="showMore" class="ui button">Show More</button>';
  
  if ($num > 4)
    echo '<i id="scrollTop" class="up arrow circular inverted large link icon"></i>';
}
