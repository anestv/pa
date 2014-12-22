<main class="center940">

<h1 class="ui red inverted block top attached header">
  <i class="trash icon"></i> Delete your account
</h1>
<div class="ui attached segment">
  <p class="ui vertical segment">You are about to delete your account. Your profile and
   questions you have answered will be erased. Questions you have asked anonymously will
   be kept as they are, but questions you have asked publicly (i.e. your name appears in
   the question) will no longer show your name.</p>
  <p class="ui vertical segment">For a 7-day period the information mentioned above will
   be kept hidden, and will be recoverable by logging in your account. After that period,
   the changes described above will take place and they will be IRREVOCABLE and PERMANENT.
   We will not be able to recover any data after that period.</p>
  <p class="ui vertical segment">If you want to procceed, confirm the account's deletion
   by typing your username and password.</p>
</div>

<form method="post" class="ui bottom attached form segment <?php if ($GLOBALS['warnMessage']) echo 'error';?>">
  <div class="ui error message">
    <?=$GLOBALS['warnMessage']?>
  </div>
  <div class="ui two fields">
    <div class="field"><div class="ui left labeled icon input">
      <i class="user icon"></i>
      <input type="text" placeholder="Username" name="user" required>
      <div class="ui corner label">
        <i class="red asterisk icon"></i>
      </div>
    </div></div>
    <div class="field"><div class="ui left labeled icon input">
      <i class="lock icon"></i>
      <input type="password" placeholder="Password" name="pass" required>
      <div class="ui corner label">
        <i class="red asterisk icon"></i>
      </div>
    </div></div>
  </div>
  <div class="ui centered buttons">
    <a class="ui labeled icon button" id="butCancel" href="." onclick="history.back();return history.length<2;">
      <i class="close icon"></i>
      <span>Cancel</span>
    </a>
    <button type="submit" class="ui right labeled icon negative button">
      <span>Delete</span>
      <i class="trash icon"></i>
    </button>
  </div>
</form>

</main>
