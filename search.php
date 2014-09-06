<!DOCTYPE html>
<html>
<head>
<title>Search - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
<link rel="stylesheet" type="text/css" href="search.css">
<meta charset="utf-8">
</head>
<body>


<div id="topNav">
  <!-- top nav bar -->
</div>

<?php
function get($prop){
  if (isset($_GET[$prop]))
    return 'value="'.$_GET[$prop].'"';
  else return '';
}
?>

<!-- bar with search options -->
<aside>
  <form name="user">
    <input type="hidden" name="lookfor" value="u">
    <h4>Search for users</h4>
    <br>
    <input type="text" name="username" maxlength="20" placeholder="Username" <?=get('username')?>><br>
    <input type="text" name="realname" maxlength="40" placeholder="Real name" <?=get('realname')?>><br>
    <input type="submit">
  </form>
  <hr><!-- TODO use tabs, if it is easy and better, but looks good anyway -->
  <form name="qa">
    <input type="hidden" name="lookfor" value="qa">
    <h4>Search for questions / answers</h4>
    <br>
    <input type="text" name="fromuser" maxlength="20" placeholder="From who" <?=get('fromuser')?>>
    <img src="res/warning.svg" title="Unless you search for questions you 
    asked, only eponymously asked questions will show up"><br>
    <input type="text" name="touser" maxlength="20" placeholder="To who" <?=get('touser')?>><br>
    <input type="text" name="query" placeholder="Text to look for" <?=get('query')?>><br>
    <span class="small">When was the question answered?</span><br>
    <input type="month" name="timeanswered" placeholder="In format yyyy-mm" <?=get('timeanswered')?>><br>
    <input type="submit">
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
  
  echo '<h2>User search</h2>';
  echo 'Found '.$res->num_rows . ($res->num_rows === 1 ? ' result' : ' results');
?>
<table id="userList">
<thead><tr>
  <th>Username</th>
  <th>Real Name</th>
</tr></thead>
<tbody>
  
  <?php 
  while ($row = $res->fetch_array())
    echo '<tr><td><a href="user/'.$row['username'].'">'.$row['username'].'</a><td>'.$row['realname'];
  ?>
  
</tbody>
</table>
  <?php 
  
}

function printDate($q, $prop){
  $time = strtotime($q[$prop]);
  $res = '<time title="'.date('r', $time).'" datetime="'.date('c',$time);
  return $res .'">'.date('G:i \o\n l j/n/y', $time) .'</time>';
}
function printQ($q){
	echo '<div class="question"><div class="links">';
	echo '<a class="orange" href="reportq.php?qid='. $q['id'] .'">Flag</a>';
	echo '</div>To: <a href="user/'.$q['touser'].'">'.$q['touser'] ."</a>";
	if ($q['publicasker'] and ($q['fromuser'] !== 'deleteduser'))
		echo ' From: <a href="user/'.$q['fromuser'].'">'.$q['fromuser']."</a>";
	echo '<br><a class="date" href="question/'. $q['id'] .'">Answered: ';
	echo printDate($q, 'timeanswered') .'</a><br><h2>';
	echo $q['question'] .'</h2><p>'.$q['answer'] .'</p></div>';
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
  echo 'Found '.$res->num_rows .($res->num_rows === 1 ? ' result':' results');
  echo '<div id="qContainer">';
  
  if ($res->num_rows === 0)
    echo '<div id="noQs">There are no questions matching your criteria</div>';
  else 
    while ($row = $res->fetch_assoc())
      printQ($row);
  
  echo '</div>';
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

</body>
</html>
