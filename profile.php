<!DOCTYPE html>
<html>
<head prefix="og: http://ogp.me/ns# profile: http://ogp.me/ns/profile#">
<?php 

try {
  if (empty($_GET['user']))
    throw new Exception('Required parameters were not provided', 400);
  
  $ownerName = $con->real_escape_string($_GET['user']);
  $owner = $con->query("SELECT * FROM users WHERE username = '$ownerName';")->fetch_array();
  if ($owner === null)
    throw new Exception('This user does not exist or has deleted their account', 404);
  
  if ($owner['deleteon'] !== null)
    throw new Exception('This user has deactivated their account.');
  
} catch (Exception $e) {
  include_once("included/profileError.php");
  die; //just in case it hasn't already
}

$ownerName = $owner['username']; //proper case (capital or small)
?>
<!-- ola ta links einai relative to /pa/. Aparaithto gia to /pa/user/aaaa -->
<base href="/pa/">
<title><?=$ownerName?> - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="css/general.css">
<link rel="stylesheet" type="text/css" href="res/fonts/customFonts.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.19.3/css/semantic.min.css">
<link rel="stylesheet" type="text/css" href="profileAppearance.dcss.php?user=<?=$ownerName?>">
<meta charset="UTF-8"><!-- an exei elhnika -->

<meta property="og:type" content="profile">
<meta property="og:site_name" content="PrivateAsk">
<meta property="og:title" content="<?=$owner['realname']?> on PrivateAsk">
<meta property="og:url" content="http://<?=$_SERVER['SERVER_ADDR']?>/pa/user/<?=$ownerName?>">
<meta property="profile:username" content="<?=$ownerName?>">

<style>
  header#profileHeader {
    border-radius: 2em;
  }
  #profileHeader > div {
    margin: 0 1.3em;
    min-height: 2.5em;
  }
  #profileHeader > div:last-child > a {
    transform: translateY(-30%);
  }
</style>

</head>
<body data-owner="<?=$ownerName?>">

<?php

$see = $owner['whosees'];
$ask = $owner['whoasks'];

//does the owner have user as friend?
$res = $con->query("SELECT friend FROM friends WHERE `user` = '$ownerName' AND friend = '$user';");
$ownerHasUserFriend = (($user === $ownerName) or ($res and $res->num_rows > 0));

//does the user have owner as friend?
$res = $con->query("SELECT friend FROM friends WHERE `user` = '$user' AND friend = '$ownerName';");
$userHasOwnerFriend = ($res and $res->num_rows > 0);

?>
<header id="profileHeader">
  <div><i class="user big circular icon"></i><h1><?=$owner['realname']?></h1></div>
  <div>
    <?php
    if ($user and $user !== $ownerName){
      if ($userHasOwnerFriend)
        echo '<a class="ui active toggle labeled right floated left icon button" '.
          'href="friends.php"><i class="user icon"></i><span>Friend</span></a>';
      else
        echo '<a class="ui toggle labeled right floated left icon button" '.
          'href="friends.php"><i class="user icon"></i><span>Add friend</span></a>';
    } ?>
    <span>Username: <?=$ownerName?></span>
  </div>
</header>
<?php

if (empty($user) and $ask !== 'all')
  echo '<div class="ui large warning message"><i class="warning icon">'.
    '</i>You must <a href="login.php">log in</a> to ask a question</div>';
else if ($ask === 'friends' and !$ownerHasUserFriend)
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
  echo '<div class="center480 ui info message"><i class="info icon"></i>';
  echo 'There are no questions to show</div>';
}
http_response_code(200);
echo '</div>';

if (isset($results)){
  if ($results > 10)
    echo '<button id="showMore" class="ui button">Show More</button>';
  
  if ($results > 4)
    echo '<i id="scrollTop" class="up arrow circular inverted large link icon"></i>';
}
?>

<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.19.3/javascript/semantic.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.age/1.2.4/jquery.age.min.js"></script>
<script src="js/profile.js"></script>
</body>
</html>
