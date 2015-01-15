</div>
<?php
if ($data['scripts']['jquery'])
  echo '<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>';
if ($data['scripts']['semantic'])
  echo '<script src="https://pa-anestv-1.c9.io/node_modules/semantic-ui/dist/semantic.min.js"></script>'; //TODO replace with url of the build
if ($data['scripts']['jquery.age'])
  echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.age/1.2.4/jquery.age.min.js"></script>';

if (isset($data['scripts']['custom']))
  foreach ($data['scripts']['custom'] as $script)
    echo "<script src='resources/js/$script'></script>\n";
?>
<footer class="ui small borderless menu">
  <span class="item">&copy; PrivateAsk 2015</span>
  <a href="terms" class="item">Terms - Privacy</a>
  <span class="right menu">
    <a href="https://github.com/anestv/pa" class="item">Source code</a>
    <a class="item" href="<?=CONTACT_URL?>" target="_blank">Contact</a>
  </span>
</footer>

</body>
</html>