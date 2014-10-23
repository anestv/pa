<?php 

header('Content-Type: text/css', true);

function termin($reason = '', $code = 0){
  if ($code !== 0) http_response_code($code);
  header("X-Error-Descr: $reason");
  die("/* Fatal error: $reason */");
}

if (empty($_GET['user'])){
  if ($user) $ownerName = $user;
  else termin('You did not specify a user', 400);
} else
  $ownerName = $con->real_escape_string($_GET['user']);

$query = "SELECT headcolor, textfont, backcolor FROM users WHERE username = '$ownerName';";
$owner = $con->query($query)->fetch_array();
if ($owner === null)
  termin('This user does not exist or has deleted their account', 404);
?>

header#profileHeader,#displayPreview span {
  background-color: <?=$owner['headcolor']?>;
  color: <?=$owner['backcolor']?>;
  /* must not be !important so that jQuery can
     change it at the preview (settings.php) */
}

body[data-owner],#displayPreview {
  font-family: '<?=$owner['textfont']?>', Calibri, Arial, sans-serif;
  background-color: <?=$owner['backcolor']?>;
  background-image: linear-gradient(to bottom,
    rgba(0, 0, 0, 0), rgba(60, 60, 60, 0.2));
}