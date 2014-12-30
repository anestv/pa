<?php
if ($data['scripts']['jquery'])
  echo '<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>';
if ($data['scripts']['semantic'])
  echo '<script src="https://preview.c9.io/anestv/pa/dev-semantic/dist/semantic.min.js"></script>'; //TODO replace with url of the build
if ($data['scripts']['jquery.age'])
  echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.age/1.2.4/jquery.age.min.js"></script>';

if (isset($data['scripts']['custom']))
  foreach ($data['scripts']['custom'] as $script)
    echo '<script src="'. helpers\Url::get_template_path() ."js/$script\"></script>\n";
?>
</body>
</html>