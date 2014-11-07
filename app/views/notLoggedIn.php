<a id="githubribbon" href="https://github.com/anestv/pa">Find me on GitHub</a>

<h1 class="ui black huge block header">
<img src="res/logo.svg" alt="PrivateAsk logo" height="100">
<div class="content">Welcome to PrivateAsk!
<h3 class="sub header">Like Ask, but private!</h3></div></h1>


<p class="ui floating info message">You control who can ask you and who can see what you answer!</p>
<p class="ui floating warning message">Please understand that the site is currently being developed, 
so bugs may exist and errors may frequently occur.</p>

<div class="ui two column middle aligned relaxed grid segment">
  <div class="column"><h3 class="ui top attached header">Log in</h3>
    <form action="login" method="POST" class="ui bottom attached form segment">
      <div class="ui left labeled field icon input">
        <input name="user" required type="text" placeholder="Username" tabindex="1" autofocus>
        <i class="user icon"></i>
        <div class="ui corner label"><i class="red asterisk icon"></i></div>
      </div>
      <div class="ui left labeled field icon input">
        <input name="pass" required type="password" placeholder="Password" tabindex="2">
        <i class="lock icon"></i>
        <div class="ui corner label"><i class="red asterisk icon"></i></div>
      </div>
      <button type="submit" class="ui right floated labeled icon positive button" tabindex="4">
        <i class="sign in icon"></i>Log in
      </button>
      <div class="ui toggle checkbox">
        <input type="checkbox" name="keep" id="keep" tabindex="3">
        <label for="keep">Keep me logged in</label>
      </div>
  </form></div>
  <div class="center aligned column">
    <a class="huge green ui labeled icon button" href="register" tabindex="5">
      <i class="signup icon"></i>Register
    </a>
  </div>
  <div class="ui vertical divider">Or</div>
</div>
