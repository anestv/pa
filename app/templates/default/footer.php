<?php
if ($data['scripts']['jquery'])
  echo '<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>';
if ($data['scripts']['jquery.address'])
  echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.address/1.6/jquery.address.min.js"></script>';
if ($data['scripts']['semantic'])
  echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.19.3/javascript/semantic.min.js"></script>';
if ($data['scripts']['jquery.age'])
  echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.age/1.2.4/jquery.age.min.js"></script>';

if (isset($data['scripts']['custom']))
  foreach ($data['scripts']['custom'] as $script)
    echo '<script src="'. helpers\url::get_template_path() ."js/$script\"></script>\n";
?>
</body>
</html>