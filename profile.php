﻿<html>
<head prefix="og: http://ogp.me/ns# profile: http://ogp.me/ns/profile#">
<?php 

if (empty($_GET['user']))
  terminate('Required parameters were not provided', 400);

$ownerName = $con->real_escape_string($_GET['user']);
$owner = $con->query("SELECT * FROM users WHERE username = '$ownerName';")->fetch_array();
if ($owner === null) terminate('This user does not exist or has deleted their account', 404);
$ownerFr = json_decode($owner['friends']);
if ($ownerFr === null) terminate('A server error has occurred.', 500);
array_push($ownerFr, $ownerName);
if ($owner['deleteon'] !== null)
  terminate('This user has deactivated their account.');
//TODO πως θα κανει τερμινατε; δεν εχει καν stylesheets και ειμαι μεσα στο head
//TODO να αλλαξω τα ownTmp σε ownerName παρακατω

?>
<!-- ola ta links einai relative to /pa/. Aparaithto gia to /pa/user/aaaa -->
<base href="/pa/">
<title><?=$ownerName?> - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
<link rel="stylesheet" type="text/css" href="res/fonts/customFonts.css">
<link rel="stylesheet" type="text/css" href="profileAppearance.dcss.php?user=<?=$ownerName?>">
<meta charset="UTF-8"><!-- an exei elhnika -->

<meta property="og:type" content="profile">
<meta property="og:site_name" content="PrivateAsk">
<meta property="og:title" content="<?=$owner['realname']?> on PrivateAsk">
<!-- <meta property="og:url" content="http://privateask.noip.me/pa/user/<?=$ownerName?>"> 
prokalei kuklous otan den einai auto ths selidas -->
<meta property="profile:username" content="<?=$ownerName?>">

</head>
<body data-owner="<?=$ownerName?>">

<?php

$see = $owner['whosees'];
$ask = $owner['whoasks'];

echo '<header><h1>'.$owner['realname']. "</h1><span>Username: $ownerName</span></header>";


if (empty($user) and $ask !== 'all')
  echo '<div class="warn">You must <a href="login.php">'.
      'log in</a> to ask a question</div>';
else if ($ask === 'friends' and !in_array($user, $ownerFr))
  echo '<div class="warn">Sorry, you do not have'.
      ' the right to ask a question</div>';
else {
?>
<form method="post" class="ask" name="askForm" action="sent.php">
<input type="hidden" name="to" value="<?=$ownerName?>"><textarea 
name="question" placeholder="Ask a question" required maxlength="200">
</textarea><div id="askControls">
<?php 
  if ($user === $ownerName)
    echo "<i>Others will <u>see that you asked</u> this question</i>";
  else if ($user)
    echo '<div><input type="checkbox" name="pubAsk" id="publicaskcheckbox">
    <label for="publicaskcheckbox">Show that I asked this question</label></div>';
  
  echo '<input type="submit" value="Submit"></div></form>';
}

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

<script src="js/jquery2.min.js"></script>
<script src="js/profile.js"></script>
</body>
</html>
