<main class="center940">
<h1 class="ui center aligned purple header top attached">
  Create an account on PrivateAsk
</h1>

<form method="post" class="ui form bottom attached segment <?php if($GLOBALS['warnMessage']) echo 'error ';?>">
<div class="ui error message">
  <?=$GLOBALS['warnMessage']?>
</div>
<div class="ui two column stackable grid">
  
  <div class="column">
    <div class="field">
      <div class="ui blue pointing below label">Username: 5 - 20 English letters and numbers</div>
      <input type="text" maxlength="20" required name="username" placeholder="Username" pattern="\w{5,20}" autofocus>
    </div>
    <div class="field">
      <div class="ui blue pointing below label">Real Name: Up to 40 characters</div>
      <input type="text" required maxlength="40" name="real" placeholder="Real Name">
    </div>
    <?php if (ENABLE_CAPTCHA): ?>
    <div class="g-recaptcha" data-sitekey="<?=RECAPTCHA_SITEKEY ?>"></div>
    <?php endif; ?>
  </div>
  
  <div class="column">
    <div class="field">
      <div class="ui blue pointing below label">Password: 6 - 100 characters</div>
      <input type="password" maxlength="101" required name="password" placeholder="Password" pattern=".{6,}">
    </div>
    <div class="field">
      <div class="ui blue pointing below label">Enter something random</div>
      <input type="text" maxlength="50" name="rand" placeholder="randomness" required autocomplete="off">
    </div>
    <div class="inline field">
      <div class="ui checkbox">
        <input type="checkbox" required id="ToScheck" name="ToS">
        <label for="ToScheck">I agree to the <a href="terms">Terms and Conditions</a></label>
      </div>
    </div>
    <button type="submit" class="ui animated positive button">
      <div class="visible content">Register</div>
      <div class="hidden content"><i class="signup icon"></i></div>
    </button>
  </div>
</div>

<div id="mperdevoBots"><!-- kind of honeypot trap for bots -->
  <input type="text" name="datebirth" maxlength="20" placeholder="You shall not  fill this field">
</div>

</form>
</main>

<!--TODO move the script somewhere else-->
<?php if (ENABLE_CAPTCHA): ?>
<script src="https://www.google.com/recaptcha/api.js"></script>
<?php endif; ?>
