<?php

if ($GLOBALS['warnMessage'])
  echo '<div class="ui error message">'.$GLOBALS['warnMessage'].'</div>';
else
  $data['question']->writeOut(true);
?>