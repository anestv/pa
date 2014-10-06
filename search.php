<!DOCTYPE html>
<html>
<head>
  <title>Search - PrivateAsk</title>
  <link rel="stylesheet" type="text/css" href="css/general.css">
  <link rel="stylesheet" type="text/css" href="css/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="css/search.css">
  <meta charset="utf-8">
  <noscript><style>
     aside .ui.tab {display: initial;}
    .ui.tabular.menu {display: none;}
  </style></noscript>
</head>
<body>


<div id="topNav">
  <!-- top nav bar -->
</div>

<?php
function get($prop){
  echo 'name="'. $prop .'"';
  if (!empty($_GET[$prop]))
    echo ' value="'.$_GET[$prop].'"';
}

if (isset($_GET['lookfor']) and $_GET['lookfor'] === 'qa'){
  $activeQA = ' active';
  $activeU = '';
} else {
  $activeQA = '';
  $activeU = ' active';
}
?>

<!-- bar with search options -->
<aside>
  <div class="ui tabular menu">
    <a class="item<?=$activeU?>" data-tab="users">Users</a>
    <a class="item<?=$activeQA?>" data-tab="qa">Questions</a>
  </div>
  <!-- name attribute for inputs is inserted by get() -->
  
  <form name="user" class="ui form<?=$activeU?> tab" autocomplete="off" data-tab="users">
    <input type="hidden" name="lookfor" value="u">
    <h4 class="ui header"><i class="users icon"></i>Search for users</h4>
    <input type="text" maxlength="20" placeholder="Username" <?=get('username')?>>
    <input type="text" maxlength="40" placeholder="Real name" <?=get('realname')?>>
    <button class="ui animated button">
      <div class="visible content">Search</div>
      <div class="hidden content"><i class="search icon"></i></div>
    </button>
  </form>
  
  <form name="qa" class="ui form<?=$activeQA?> tab" autocomplete="off" data-tab="qa">
    <input type="hidden" name="lookfor" value="qa">
    <h4 class="ui header"><i class="question icon"></i>Search for questions / answers</h4>
    <div class="ui icon input">
      <input type="text" maxlength="20" placeholder="From who" <?=get('fromuser')?>>
      <i class="info icon" title="Unless you search for questions you asked, only eponymously asked questions will show up"></i>
    </div>
    <input type="text" maxlength="20" placeholder="To who" <?=get('touser')?>>
    <input type="text" placeholder="Text to look for" <?=get('query')?>>
    <span class="small">When was the question answered?</span>
    <input type="month" placeholder="In format yyyy-mm" <?=get('timeanswered')?>>
    <button class="ui animated button">
      <div class="visible content">Search</div>
      <div class="hidden content"><i class="search icon"></i></div>
    </button>
  </form>
</aside>


<!-- results -->
<main>
  
<?php

function escape($str) {
  global $con;
  $a = preg_replace('/(_|%|\|)/', '|\1', $str);
  return $con->real_escape_string($a);
}

function doUserSearch() {
  global $con;
  
  $username = isset($_GET['username']) ? $_GET['username'] : '';
  $realname = isset($_GET['realname']) ? $_GET['realname'] : '';
  
  if ($username and strlen($username) < 3)
    terminate('Enter at least 3 characters at "Username"', 400);
  if ($realname and strlen($realname) < 3)
  	terminate('Enter at least 3 characters at "Real Name"', 400);
  
  $query = "SELECT username, realname FROM users WHERE username LIKE '".
      escape($username)."%' AND realname LIKE '".escape($realname)."%';";
  $res = $con->query($query);
  if (!$res) terminate('An error occured '.$con->error, 500);
?>
<h2>User search</h2>

<table id="userList" class="ui collapsing padded table segment">
<thead><tr>
  <th>Username</th>
  <th>Real Name</th>
  <th>Link</th>
</tr></thead>
<tbody>
  
  <?php 
  while ($row = $res->fetch_array()){
    echo '<tr><td>'.$row['username'].'</a><td>'.$row['realname'];
    echo '<td><a href="user/'.$row['username'].'"><i class="url teal link icon"></i></a>';
  }?>
  
</tbody>
<tfoot><tr>
  <?php 
  if ($res->num_rows > 0){
    echo '<th colspan="3" class="ui info message"><i class="checkmark icon"></i>Found ';
    echo $res->num_rows .' result'. ($res->num_rows === 1 ? '':'s') . '</th>';
  } else {
    echo '<th colspan="3" class="ui warning message">';
    echo '<i class="warning icon"></i>Did not find any results</th>';
  }?>
</tr></tfoot>
</table>
<?php 
}

function printDate($q, $prop){
  $time = strtotime($q[$prop]);
  $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
  return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
}
function printQ($q){
  echo '<div class="question"><div class="ui top attached tiny header">';
  echo 'To: <a href="user/'. $q['touser'].'">'. $q['touser'] .'</a>&emsp;';
  if ($q['publicasker'] and ($q['fromuser'] !== 'deleteduser'))
    echo 'From: <a href="user/'.$q['fromuser'].'">'.$q['fromuser'] .'</a>';
  echo '<a class="date" href="question/'. $q['id'] .'">Answered: ';
  echo printDate($q, 'timeanswered') . '</a></div>';
  echo '<div class="ui piled bottom attached segment"><div class="links">';
  echo '<a href="reportq.php?qid='.$q['id'].'"><i class="red flag link icon"></i></a>';
  
  echo '</div><h3 class="ui header">' . $q['question'] .'</h3>';
  echo '<p>'. $q['answer'] . '</p></div></div>';
}

function doQASearch() {
  global $con, $user;

  $query = "SELECT * FROM questions WHERE answer IS NOT NULL";
  
  if (!empty($_GET['query'])){
    if (strlen(trim($_GET['query'])) < 5)
      terminate('Please enter at least five characters as a query', 400);
    
    $escapedQuery = $con->real_escape_string($_GET['query']);
    $query .= " AND MATCH(question, answer) AGAINST ('$escapedQuery')";
  }
  
  
  if (empty($_GET['fromuser'])) $fromuser = '';
  else if (preg_match('/^\w{5,20}$/', $_GET['fromuser']) === 1){
    $fromuser = $_GET['fromuser'];
    if ($fromuser === 'deleteduser') $fromuser = '-';
    // - is not a valid username, so no results will return
  } else terminate("Enter a valid username at field 'From user'", 400);
  
  if (empty($_GET['touser'])) $touser = '';
  else if (preg_match('/^\w{5,20}$/', $_GET['touser']) === 1)
  	$touser = $_GET['touser'];
  else terminate("Enter a valid username at field 'To user'", 400);
  
  if (!empty($_GET['timeanswered'])){
    if (preg_match("/^(1|2)\\d{3}-(0[1-9]|10|11|12)$/", $_GET['timeanswered']) === 1){
      $date = $_GET['timeanswered'] . '-01';
      $query .= " AND timeanswered BETWEEN '$date' AND '$date' + INTERVAL 1 MONTH - INTERVAL 1 DAY";
    } else terminate('Enter the month in the format yyyy-mm', 400);
  }
  
  if ($fromuser) $query .= " AND fromuser = '$fromuser'";
  if ($touser) $query .= " AND touser = '$touser'";
  
  $query .= " AND touser IN (SELECT username FROM users WHERE deleteon IS NULL AND (";
  if ($user){ //takes care of privacy
    $query .= "whosees = 'users' OR friends LIKE '%\"";
    $query .= escape($user)."\"%' OR username = '$user' OR ";
  }
  $query .= "whosees = 'all'))";
  
  if ($fromuser and $fromuser !== $user)
    $query .= ' AND publicasker = 1';
  
  $query .= " LIMIT 50;"; //maybe do something about the limit in the future
  
  $res = $con->query($query);
  if (!$res) terminate('Server error '.$con->error, 400);
  
  echo '<h2>Question search</h2>';
  
  if ($res->num_rows === 0){
    echo '<div class="ui warning message"><i class="warning icon">';
    echo '</i>There are no questions matching your criteria</div>';
  } else {
    echo '<div class="ui info message"><i class="checkmark icon"></i>Found ';
    echo $res->num_rows .' result'. ($res->num_rows === 1 ? '':'s') .'</div>';
    echo '<div id="qContainer">';
    while ($row = $res->fetch_assoc())
      printQ($row);
    echo '</div>';
  }
}


//check if at least one search query exist
function searchQueriesExist() {
  if ($_GET['lookfor'] === 'qa')
    $arr = array('query', 'fromuser', 'touser', 'timeanswered');
  else $arr = array('username', 'realname');
  
  foreach($arr as $curr)
    if (isset($_GET[$curr]) and trim($_GET[$curr]))
      return true;
  
  return false;
}


//an ontws psaxnei kati
if (!empty($_GET['lookfor']) and searchQueriesExist()){
  $subj = $_GET['lookfor'];
  
  switch ($subj) {
    case 'u':  doUserSearch(); break;
    case 'qa': doQASearch(); break;
    default: terminate('Invalid search terms',400);
  }
}
?>

</main>


<footer>
  <!--TODO-->
</footer>

<script src="js/jquery2.min.js"></script>
<script src="js/jquery.address.min.js"></script>
<script src="js/semantic.min.js"></script>
<script>$(function(){$('.tabular.menu .item').tab({history: false});});</script>
</body>
</html>
