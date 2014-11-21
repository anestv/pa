<main class="center940">
<?php

if ($GLOBALS['warnMessage']){
  echo '<div class="center480 aloneInPage ui error message">';
  echo $GLOBALS['warnMessage'].'</div></main>';
  return;
}

$data['q']->writeOut(true, true);
?>
  <form method="post" class="answer ui bottom attached stacked form segment">
    <textarea name="answer" placeholder="Your answer" required autofocus maxlength="200"></textarea>
    <button type="submit" class="ui positive centered labeled icon button">
      <i class="pencil icon"></i>Answer
    </button>
  </form>
</div>
</main>
