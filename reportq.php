<!DOCTYPE html>
<html>
<head>
  <title>Report a question - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <link rel="stylesheet" type="text/css" href="css/semantic.min.css">
  <meta charset="utf-8">
  <style type="text/css">
    #butCancel span {
      display: inline-block;
      opacity: 0.5;
      margin: 0 -5.3em 0 0.7em;
      transition: 0.6s ease margin-right, 0.4s linear opacity;
    }
    #butCancel:hover span {
      margin-right: 0;
      opacity: 1;
    }
  </style>
</head>
<body>
<main class="center940">
<?php

if (empty($_GET['qid']))
  terminate('Required parameters were not provided', 400);
else if((!is_numeric($_GET['qid'])) or ($_GET['qid'] <= 0))
  terminate('The question id is not of correct type.', 400);
else
  $qid = intval($_GET['qid']);

if (empty($user))
  terminate('You must log in to continue<br><a href="login.php">Log in</a>', 401);

$q = $con->query("SELECT * FROM questions WHERE id = $qid;")->fetch_array();

if (empty($q['touser']))
  terminate("The question you have requested does not exist or has been deleted.", 404);

if (empty($q['answer']))
  terminate('This question has not been answered yet.');


$ownerName = $q['touser'];
$owner = $con->query("SELECT * FROM users WHERE username = '$ownerName';")->fetch_array();
$ownerFr = json_decode($owner['friends']);
if ($ownerFr === null) terminate('A server error has occurred.', 500);
array_push($ownerFr, $ownerName);
if ($owner['deleteon'] !== null)
  terminate('The owner of this question has deactivated their account.');


$whosees = $owner['whosees'];
if ($whosees === 'friends' and !in_array($user, $ownerFr))
  terminate('Sorry, you do not have the right to see this question', 403);


if ($_SERVER["REQUEST_METHOD"] === "POST"){
  if (empty($_POST['reason']))
    terminate("You did not tell us why you report this question");
  $reason = $con->real_escape_string($_POST['reason']);
  
  if (!in_array($reason, array('illegal', 'threat', 'tos', 'porn', 'copyright', 'other')))
    terminate("Select one of the listed reasons", 400);
  
  $query = "INSERT INTO question_reports (qid, reporter, reason) VALUES ($qid, '$user', '$reason');";
  $res = $con->query($query);
  if ($res) successMsg('You have successfully reported this question');
  else errorMsg('This question could not be reported' . $con->error);

} else { //dhladh einai GET
  
  if ($user === $ownerName)
    echo '<div class="ui info message"><i class="info icon"></i> This question'.
      ' was asked to you, so we suggest you <a href="deleteq.php?qid='.
      $qid .'">delete this question</a> if it offends or annoys you</div>';
  
  //show the Q and A
  
  function printDate($prop){
    global $q;
    $time = strtotime($q[$prop]);
    if (!$time) return '-';
    $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
    return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
  }
  ?>
<div class="question">
  <div class="ui top attached tiny header">
    To: <a href="user/<?=$q['touser']?>"><?=$q['touser']?></a>
    <a class="date">Asked: <?=printDate('timeasked')?></a><br>
    <?php if ($q['publicasker'] and $q['fromuser'] !== 'deleteduser')
      echo 'From: <a href="user/'. $q['fromuser'] .'">'. $q['fromuser'] .'</a>' ?>
    <a class="date" href="question/<?=$qid?>">Answered: <?=printDate('timeanswered')?></a>
  </div>
  <div class="ui attached segment">
    <h3 class="ui header"><?=$q['question']?></h3>
    <p><?=$q['answer']?></p>
  </div>
  <form method="post" class="ui bottom attached form segment">
    <div class="ui visible warning message">
      <h3>Report this question</h3>
      What is wrong with this question / answer?
    </div>
    <div class="ui two column stackable grid">
      <div class="grouped inline fields column">
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri1" name="reason" value="illegal" required><!--one of all is required-->
          <label for="ri1">It contains illegal stuff</label>
        </div></div>
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri2" name="reason" value="threat">
          <label for="ri2">It clearly threatens someone</label>
        </div></div>
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri3" name="reason" value="tos">
          <label for="ri3">It violates the site's <a href="terms.html">Terms of Service</a></label>
        </div></div>
      </div>
      <div class="grouped inline fields column">
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri4" name="reason" value="porn">
          <label for="ri4">It contains or links to porn</label>
        </div></div>
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri5" name="reason" value="copyright">
          <label for="ri5">It contains copyrighted material</label>
        </div></div>
        <div class="field"><div class="ui radio checkbox">
          <input type="radio" id="ri6" name="reason" value="other">
          <label for="ri6">Something else</label>
        </div></div>
      </div>
    </div>
    <div class="ui centered buttons">
      <a class="ui icon button" id="butCancel" href="index.php" onclick="history.back();return history.length<2;">
        <i class="close icon"></i>
        <span>Cancel</span>
      </a> 
      <button type="submit" class="ui right labeled icon orange button">
        <span>Report</span>
        <i class="flag icon"></i>
      </button>
    </div>
  </form>
</div>
<?php } ?>
</main>
</body>
</html>
