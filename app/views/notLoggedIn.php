<div class="ui page grid"><div class="centered column"><div class="ui padded grid">

<header class="row">

<div class="eight wide computer eight wide tablet sixteen wide mobile column">
  <h1 class="ui inverted header">
    Welcome to PrivateAsk!
  </h1>
  <h5 class="ui inverted header">You control who can ask you and who can see what you answer!</h5>
</div>
<div class="four wide computer four wide tablet eight wide mobile center aligned column">
  <img src="images/unstable.png" alt="Unstable version" id="unstable" title="This site is under development">
</div>
<div class="four wide computer four wide tablet eight wide mobile column">
  <a href="https://github.com/anestv/pa" class="ribbon">
    <div class="stitches-top"></div>
    <strong class="content"><h1>Find us on GitHub</h1></strong>
    <div class="stitches-bottom"></div>
  </a>
</div>

</header>

<?php
if ($_SESSION['deleteAccSuccess']){
  unset($_SESSION['deleteAccSuccess']);
  echo '<div class="sixteen wide column">
  <div class="ui large info message">
    <i class="info icon"></i>
    Your account has been deactivated and will be deleted in 7 days unless you log in
  </div></div>';
}
?>

<div class="row">

<div class="ui middle aligned relaxed stackable grid">
  <div class="eight wide column">
    <h3 class="ui top attached block header">Log in</h3>
    <form action="login" method="POST" class="ui bottom attached form segment">
      <div class="ui left icon input field">
        <i class="user icon"></i>
        <input name="user" required type="text" placeholder="Username" autofocus>
      </div>
      <div class="ui left icon input field">
        <i class="lock icon"></i>
        <input name="pass" required type="password" placeholder="Password">
      </div>
      <div class="ui toggle checkbox">
        <input type="checkbox" name="keep" id="keep">
        <label for="keep">Keep me logged in</label>
      </div>
      <button type="submit" class="ui large right floated right labeled icon primary button">
        Log in
        <i class="sign in icon"></i>
      </button>
    </form>
  </div>
  <div class="ui inverted vertical divider">Or</div>
  <div class="eight wide center aligned column">
    <a href="register" class="ui big primary button">Register</a>
    <div class="ui hidden divider"></div>
    <a href="<?=$data['fbLoginUrl']?>" class="ui big facebook button">Login with Facebook</a>
  </div>
</div>

</div>

</div></div></div>
