<div id="error">
  <h1>Has he lost his mind?</h1>
  <h6>Can he see or is he blind?</h6>
  <p>The server may have lost his mind. Sorry for that.</p>
  <?php if ($data['error']) echo "<blockquote>{$data['error']}</blockquote>"; ?>
  <p>Please try again in a little bit. Or go to <a href=".">the homepage</a></p>
</div>

<footer id="copyright">
  <?=$data['imagedata']?>
</footer>
