<!DOCTYPE html>
<html>
<head>
  <title>Answer - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.19.3/css/semantic.min.css">
  <meta charset="utf-8">
</head>
<body>

<?php

requireLogin();

try {
  
  if (empty($_GET['qid']))
    throw new Exception('Required parameters were not provided', 400);
  else if (!is_numeric($_GET['qid']) or ($_GET['qid'] <= 0))
    throw new InvalidArgumentException('The question id is not of correct type.', 400);
  else
    $qid = intval($_GET['qid']);
  
  $q = $con->query("SELECT * FROM questions WHERE id = $qid;")->fetch_array();
  
  if (empty($q['touser']))
    throw new Exception("The question you have requested does not exist or has been deleted.", 404);
  
  if ($q['touser'] !== $user)
    throw new Exception("This question was not asked to you, so you cannot answer it.", 403);
  
  if (!empty($q['answer'])) //an exei hdh apanthsh
    throw new Exception("You have already answered this question; you can't reanswer it", 405);
  
} catch (Exception $e) {
  terminate($e->getMessage(), $e->getCode());
}

if (isset($_POST['answer']) and trim($_POST['answer'])) {
  try {
    $answer = $con->real_escape_string(htmlspecialchars($_POST['answer']));
    
    $query = "UPDATE questions SET answer = '$answer', timeanswered = NOW() WHERE id = $qid ;";
    $result = $con->query($query);
    
    if (!$result) 
      throw new RuntimeException($con->error);
    
    successMsg("You have successfully answered!");
    redirect("question/$qid", 201); //won't really redirect, just set Location
    //TODO maybe better to redirect 302 to pending.php
    die('</body></html>');
    
  } catch (Exception $e) {
    handleException($e);
  }
}

function printDate($prop){
  global $q;
  $time = strtotime($q[$prop]);
  $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
  return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
}
?>

<div class="question" id="qContainer">
  <div class="ui top attached tiny header">
    <?php if ($q['publicasker'] and $q['fromuser'] !== 'deleteduser')
      echo 'From: <a href="user/'.$q['fromuser'].'">'.$q['fromuser'] ."</a>"; ?>
    <a class="date">Asked: <?=printDate('timeasked')?></a>
  </div>
  <div class="ui attached segment">
    <div class="links"><a class="deleteq" href="deleteq.php?qid=<?=$qid?>">
      <i class="red trash link icon"></i>
    </a></div>
    <h3 class="ui header"><?=$q['question']?></h3>
  </div>
  
  <form method="post" class="answer ui bottom attached stacked form segment">
    <textarea name="answer" placeholder="Your answer" required autofocus maxlength="200"></textarea>
    <button type="submit" class="ui positive centered labeled icon button">
      <i class="pencil icon"></i>Answer
    </button>
  </form>
  
</div>

</body>
</html>
