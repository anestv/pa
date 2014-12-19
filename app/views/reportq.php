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

  <form method="post" class="ui bottom attached form segment">
    <div class="ui visible warning message">
      <h3>Report this question</h3>
      What is wrong with this question / answer?
    </div>
    <div class="ui two column stackable grid">
      <div class="grouped inline fields column">
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri1" name="reason" value="illegal" required><!--one of all is required-->
          <label for="ri1">It contains illegal stuff</label>
        </div></div>
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri2" name="reason" value="threat">
          <label for="ri2">It clearly threatens someone</label>
        </div></div>
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri3" name="reason" value="tos">
          <label for="ri3">It violates the site's <a href="terms">Terms of Service</a></label>
        </div></div>
      </div>
      <div class="grouped inline fields column">
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri4" name="reason" value="porn">
          <label for="ri4">It contains or links to porn</label>
        </div></div>
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri5" name="reason" value="copyright">
          <label for="ri5">It contains copyrighted material</label>
        </div></div>
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri6" name="reason" value="other">
          <label for="ri6">Something else</label>
        </div></div>
      </div>
    </div>
    <div class="ui centered buttons">
      <a class="ui icon button" id="butCancel" href="." onclick="history.back();return history.length<2;">
        <i class="close icon"></i>
        <span>Cancel</span>
      </a> 
      <button type="submit" class="ui right labeled icon orange button">
        <span>Report</span>
        <i class="flag icon"></i>
      </button>
    </div>
  </form>
</div>

</main>
