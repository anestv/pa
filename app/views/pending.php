<main id="qContainer">

<?php

if ($GLOBALS['warnMessage']){
  echo '<div class="aloneInPage center480 ui error message">';
  echo $GLOBALS['warnMessage'].'</div>';
  return;
}

if (isset($data['questions']) and is_array($data['questions']))
  $count = count($data['questions']);
else $count = 0;

echo '<div class="ui large info message"><i class="info icon"></i>';
echo 'Welcome back, '.$GLOBALS['user']->username .'! There ';

if ($count === 0)
  echo 'are no more questions';
else if ($count === 1)
  echo 'is 1 question';
else if ($count === 50)
  echo 'are more than 50 questions';
else
  echo "are $count questions";

echo ' for you to answer</div>';

if ($_SESSION['answerSuccess']){
  echo '<div class="ui success message"><i class="checkmark icon"></i>'.
    'You have answered question #'. $_SESSION['answerSuccess'].'</div>';
  
  unset($_SESSION['answerSuccess']);
}

if ($count)
  foreach($data['questions'] as $q)
    $q->writeOut(false, false);

?>
</main>
