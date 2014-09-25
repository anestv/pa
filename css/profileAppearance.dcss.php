<?php 

header('Content-Type: text/css', true);

function termin($reason = '', $code = 0){
  if ($code !== 0) http_response_code($code);
  header("X-Error-Descr: $reason");
  die("/* Fatal error: $reason */");
}

if (empty($_GET['user']))
  termin('You did not specify a user', 400);

$ownerName = $con->real_escape_string($_GET['user']);
$query = "SELECT textcolor, textfont, backcolor FROM users WHERE username = '$ownerName';";
$owner = $con->query($query)->fetch_array();
if ($owner === null)
  termin('This user does not exist or has deleted their account', 404);
?>

* {
  color: <?=$owner['textcolor']?>;
  font-family: '<?=$owner['textfont']?>', Calibri, Arial, sans-serif;
}

header, div.question, button, #noQs, .warn, #askControls {
  background-color: <?=$owner['backcolor']?>!important;
}
