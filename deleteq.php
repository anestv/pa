<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Delete question - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.19.3/css/semantic.min.css">
  <style type="text/css">
    .ui.centered.buttons > * {
      height: 2.6em;
    }
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

requireLogin();

$q = $con->query("SELECT * FROM questions WHERE id = $qid;")->fetch_array();

if (empty($q['touser']))
  terminate("The question you have requested does not exist or has been deleted.", 404);

if ($q['touser'] !== $user)
  terminate('You cannot delete this question', 403);

function printDate($prop){
  global $q;
  $time = strtotime($q[$prop]);
  if (!$time) return '-';
  $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
  return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
}

if (!empty($_POST['del'])){
  try {
    $del = $con->query("DELETE FROM questions WHERE id = $qid;");
    if (!$del)
      throw new RuntimeException($con->error);
    
    successMsg('You have successfully deleted this question', '<a href=".">Home</a>');
    die('</main></body></html>');
    
  } catch (Exception $e) {
    handleException($e);
  }
}
?>

<div class="question">
  <div class="ui top attached tiny header">
    To: <a href="user/<?=$q['touser']?>"><?=$q['touser']?></a>
    <a class="date">Asked: <?=printDate('timeasked')?></a><br>
    <?php if ($q['publicasker'] and $q['fromuser'] !== 'deleteduser')
      echo 'From: <a href="user/'. $q['fromuser'] .'">'. $q['fromuser'] .'</a>' ?>
    <a class="date">Answered: <?=printDate('timeanswered')?></a>
  </div>
  <div class="ui attached segment">
    <h3 class="ui header"><?=$q['question']?></h3>
    <p><?=$q['answer']?></p>
  </div>
  <form method="POST" class="ui bottom attached segment">
    <input type="hidden" name="del" value="1">
    <div class="ui warning message">
      <h2 class="header"><i class="warning icon"></i>
        Are you sure you want to delete this question?
      </h2>
      This cannot be undone
    </div>
     
    <div class="ui centered buttons">
      <a class="ui icon button" id="butCancel" href="index.php" onclick="history.back();return history.length<2;">
        <i class="close icon"></i>
        <span>Cancel</span>
      </a> 
      <button type="submit" class="ui right labeled icon negative button">
        <span>Delete</span>
        <i class="trash icon"></i>
      </button>
    </div>
  </form>
</div>

</main>
</body>
</html>
