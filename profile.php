<!DOCTYPE html>
<html>
<head prefix="og: http://ogp.me/ns# profile: http://ogp.me/ns/profile#">
<?php 

if (empty($_GET['user']))
  terminate('Required parameters were not provided', 400);

$ownerName = $con->real_escape_string($_GET['user']);
$owner = $con->query("SELECT * FROM users WHERE username = '$ownerName';")->fetch_array();
if ($owner === null) terminate('This user does not exist or has deleted their account', 404);
$ownerFr = json_decode($owner['friends']);
if ($ownerFr === null or !is_array($ownerFr))
  terminate('A server error has occurred.', 500);
array_push($ownerFr, $ownerName);
if ($owner['deleteon'] !== null)
  terminate('This user has deactivated their account.');
//TODO πως θα κανει τερμινατε; δεν εχει καν stylesheets και ειμαι μεσα στο head

?>
<!-- ola ta links einai relative to /pa/. Aparaithto gia to /pa/user/aaaa -->
<base href="/pa/">
<title><?=$ownerName?> - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="css/general.css">
<link rel="stylesheet" type="text/css" href="res/fonts/customFonts.css">
<link rel="stylesheet" type="text/css" href="css/semantic.min.css">
<link rel="stylesheet" type="text/css" href="css/profileAppearance.dcss.php?user=<?=$ownerName?>">
<meta charset="UTF-8"><!-- an exei elhnika -->

<meta property="og:type" content="profile">
<meta property="og:site_name" content="PrivateAsk">
<meta property="og:title" content="<?=$owner['realname']?> on PrivateAsk">
<meta property="og:url" content="http://<?=$_SERVER['SERVER_ADDR']?>/pa/user/<?=$ownerName?>">
<meta property="profile:username" content="<?=$ownerName?>">

</head>
<body data-owner="<?=$ownerName?>">

<?php

$see = $owner['whosees'];
$ask = $owner['whoasks'];

echo '<header id="profileHeader"><i class="user big circular icon"></i><h1>';
echo $owner['realname']. "</h1><span>Username: $ownerName</span></header>";


if (empty($user) and $ask !== 'all')
  echo '<div class="ui large warning message"><i class="warning icon">'.
    '</i>You must <a href="login.php">log in</a> to ask a question</div>';
else if ($ask === 'friends' and !in_array($user, $ownerFr))
  echo '<div class="ui large warning message"><i class="warning icon">'.
    '</i>Sorry, you do not have the right to ask a question</div>';
else {
?>
<form method="post" class="ask ui dimmable form segment" action="sent.php">

  <div class="ui dimmer"><div class="content">
    <!-- .center is for semantic modal behavior to close on click on dimmer-->
    <div class="center ui icon header">
      <i class="green checkmark icon"></i>
      <p>Question submitted!</p>
      <div class="ui blue small button">Ask another one?</div>
    </div>
  </div></div>
  <input type="hidden" name="to" value="<?=$ownerName?>">
  <textarea name="question" placeholder="Ask a question" required maxlength="200"></textarea>
  <div id="askControls">
<?php 
  if ($user === $ownerName)
    echo "<i>Others will <u>see that you asked</u> this question</i>";
  else if ($user)
    echo '<label for="publicaskcheckbox"><input type="checkbox" name="pubAsk"
    id="publicaskcheckbox">Show that I asked this question</label>';
?>
  <button type="submit" class="ui blue small icon animated button">
    <div class="hidden content"><i class="question icon"></i></div>
    <div class="visible content">Ask</div>
  </button>
  </div>
</form>
<?php }

echo '<div id="qContainer">';
include("loadquestions.php");
if (http_response_code() === 204){
  $isLast = true;
  echo '<div class="center480 ui info message"><i class="info icon"></i>';
  echo 'There are no questions to show</div>';
}
http_response_code(200);
echo '</div>';

if (!$isLast)
  echo '<button id="showMore" class="ui button">Show More</button>';

if ($res->num_rows > 4)
  echo '<i id="scrollTop" class="up arrow circular inverted large link icon"></i>';

?>

<script src="js/jquery2.min.js"></script>
<script src="js/profile.js"></script>
<script src="js/semantic.min.js"></script>
</body>
</html>
