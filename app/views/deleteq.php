<main class="center940">

<?php 
if ($data['suggestDelete'])
  echo '<div class="ui info message"><i class="info icon"></i> This question'.
    ' was asked to you, so we suggest you <a href="question/'. $data['q']->qid.
    '/delete">delete this question</a> if it offends or annoys you</div>';

if ($GLOBALS['warnMessage']){
  echo '<div class="ui error message">'.$GLOBALS['warnMessage'].'</div></main>';
  return; //done with this file
}

$data['q']->writeOut(['extended', 'partial']);
?>

  <form method="post" class="ui bottom attached segment">
    <input type="hidden" name="del" value="1">
    <div class="ui warning message">
      <h2 class="header"><i class="warning icon"></i>
        Are you sure you want to delete this question?
      </h2>
      This cannot be undone
    </div>
    
    <div class="ui centered buttons">
      <a class="ui icon button" id="butCancel" href="." onclick="history.back();return history.length<2;">
        <i class="close icon"></i>
        <span>Cancel</span>
      </a> 
      <button type="submit" class="ui right labeled icon negative button">
        <span>Delete</span>
        <i class="trash icon"></i>
      </button>
    </div>
  </form>
</div>
</main>
