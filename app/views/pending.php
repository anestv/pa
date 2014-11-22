<main id="qContainer">

<?php

if ($GLOBALS['warnMessage']){
  echo '<div class="aloneInPage center480 ui error message">';
  echo $GLOBALS['warnMessage'].'</div>';
  return;
}

echo '<div class="ui large info message"><i class="info icon"></i>';
echo 'Welcome back, '.$GLOBALS['user']->username .'! There ';

if ($data['count'] === 0)
  echo 'are no more questions for you to answer</div>';
else {
  if ($data['count'] === 1)
    echo 'is 1 question';
  else if ($data['count'] === 50)
    echo 'are more than 50 questions';
  else
    echo 'are '.$data['count'].' questions';
  
  echo ' for you to answer</div>';
  
  foreach($data['questions'] as $q)
    $q->writeOut(false, false);
}
?>
</main>
