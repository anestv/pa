<html>
<head>
<?php $ownTmp = empty($_GET['user'])?'':htmlspecialchars($_GET['user']); ?>
<!-- ola ta links einai relative to /pa/. Aparaithto gia to /pa/user/aaaa -->
<base href="/pa/">
<!-- SOS TODO O titlos einai vulnerable an valei kaneis ?user=</title> h paromoia -->
<title><?=$ownTmp?> - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
<link rel="stylesheet" type="text/css" href="res/fonts/customFonts.css">
<link rel="stylesheet" type="text/css" href="profileAppearance.dcss.php?user=<?=$ownTmp?>">
<meta charset="UTF-8"><!-- an exei elhnika -->
</head>
<body data-owner="<?=$ownTmp?>">

<?php

if (empty($_GET['user']))
  terminate('Required parameters were not provided', 400);

$ownerName = mysqli_real_escape_string($con, $_GET['user']);
$owner = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM users WHERE username = '$ownerName';"));
if ($owner === null) terminate('This user does not exist or has deleted their account', 404);
$ownerFr = json_decode($owner['friends']);
if ($ownerFr === null) terminate('A server error has occurred.', 500);
array_push($ownerFr, $ownerName);
if ($owner['deleteon'] !== null)
  terminate('This user has deactivated their account.');


$see = $owner['whosees'];
$ask = $owner['whoasks'];


//TODO header with username and real name
echo '<header><h1>'.$owner['realname']. "</h1><span>Username: $ownerName</span></header>";


if (empty($user) and $ask !== 'all')
  echo '<div class="warn">You must <a href="login.php">'.
      'log in</a> to ask a question</div>';
else if ($ask === 'friends' and !in_array($user, $ownerFr))
  echo '<div class="warn">Sorry, you do not have'.
      ' the right to ask a question</div>';
else
  echo '<form method="post" class="ask" name="askForm" action="sent.php">
<textarea name="question" placeholder="Ask a question" required maxlength="200">
</textarea><div id="askControls">'. (empty($user)? '': '<div>
<input type="checkbox" name="pubAsk" id="publicaskcheckbox">
<label for="publicaskcheckbox">Show that I asked this question</label>
</div>').'<input type="submit" name="inpSubmit"></div></form>';


if (empty($user) and $see !== 'all')
  die('<div class="warn">You must <a href="login.php">'.
      'log in</a> to view this user&#39;s question<div>'); //TODO die h ma kapoio tropo mh proxwrhseis
else if ($see === 'friends' and !in_array($user, $ownerFr))
  die();


echo '<div id="qContainer">';
include("loadquestions.php");
if (http_response_code() === 204){
  http_response_code(200);
  $isLast = true;
  echo '<div id="noQs">There are no questions to show</div>';
}
echo '</div>';

if (!$isLast)
  echo '<button id="showMore">Show More</button>';

?>

<script src="profile.js"></script>
</body>
</html>
