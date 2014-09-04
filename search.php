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

function doQASearch() {
  global $con, $user;
  
  if (empty($_GET['query']))
    $textQ = '';
  else {
    if (strlen(trim($_GET['query'])) < 5)
      terminate('Please enter at least five characters as a query', 400);
    
    $escapedQuery = $con->real_escape_string($_GET['query']);
    $textQ = " AND MATCH(question, answer) AGAINST ('$escapedQuery')";
  }
  
  
  if (empty($_GET['fromuser']))
    $fromuser = '';
  else if (preg_match('/^\w{0,20}$/', $_GET['fromuser']) === 1)
    $fromuser = $_GET['fromuser'];
  else terminate("Enter a valid username at field 'From user'", 400);
  
  if (empty($_GET['touser']))
  	$touser = '';
  else if (preg_match('/^\w{0,20}$/', $_GET['touser']) === 1)
  	$touser = $_GET['touser'];
  else terminate("Enter a valid username at field 'To user'", 400);
  
  if (empty($_GET['timeanswered']))
    $dateQ = "";
  else if (preg_match("/^(1|2)\\d{3}-(0[1-9]|10|11|12)$/", $_GET['timeanswered']) === 1){
    $date = $_GET['timeanswered'] . '-01';
    $dateQ = " AND timeanswered BETWEEN '$date' AND '$date' + INTERVAL 1 MONTH - INTERVAL 1 DAY";
  } else terminate('Enter the month in the format yyyy-mm', 400);
  
  $query = "SELECT * FROM questions WHERE
    answer IS NOT NULL AND
    fromuser LIKE '$fromuser%' AND
    touser LIKE '$touser%' AND
    touser IN (SELECT username FROM users WHERE deleteon IS NULL)";
      //TODO SOS!! username may contain _ which means match any one character.
      //it must be escaped: \_
  
  if ($fromuser and $fromuser !== $user)
    $query .= ' AND publicasker = 1';
  
  $query .= $textQ . $dateQ . ' LIMIT 100;';
  
  $res = $con->query($query);
  
  if (!$res) terminate('Server error '.$con->error, 400);
  
  
  echo '<h2>Question search</h2>';
  // TODO add qContainer, printQ() etc...
  //var_dump($res);
  //echo '<br><br>';
  while ($row = $res->fetch_assoc()){
    var_dump($row);
    echo '<br>';
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

</body>
</html>
