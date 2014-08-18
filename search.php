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
  if (empty($username) and empty($realname))
  	terminate('You must enter a username or a real name', 400);
  
  if (!empty($username) and strlen($username) < 3)
    terminate('Enter at least 3 characters at "Username"', 400);
  if (!empty($realname) and strlen($realname) < 3)
  	terminate('Enter at least 3 characters at "Real Name"', 400);
  
  $query = "SELECT username, realname FROM users WHERE username LIKE '".
      escape($username)."%' AND realname LIKE '".escape($realname)."%';";
  $res = $con->query($query);
  if (!$res) terminate('An uknown server error occured.', 500);
  
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
    echo '<tr><td>'.$row['username'].'<td>'.$row['realname'];
  ?>
  
</tbody>
</table>
  <?php 
  
}

function doQASearch() {
  
  if (empty($_GET['timeanswered']))
    $dateQ = "";
  else if (preg_match("/^(1|2)\\d{3}-(0[1-9]|10|11|12)$/", $_GET['timeanswered']) === 1){
    $date = $_GET['timeanswered'] . '-01';
    $dateQ = " AND timeanswered BETWEEN '$date' AND '$date' + INTERVAL 1 MONTH - INTERVAL 1 DAY";
  } else terminate('Enter the month in the format yyyy-mm', 400);
  
  
  
  
}




//an ontws psaxnei kati
if (!empty($_GET['lookfor'])){
  $subj = $_GET['lookfor'];
  
  switch ($subj) {
    case 'u':  doUserSearch(); break;
    case 'qa': doQASearch(); break;
    default: terminate('Invalid search terms',400);
  }
}
?>

</main>

<!-- bar with search options -->
<aside>
  <form name="user">
    <input type="hidden" name="lookfor" value="u">
    Search for users
    <br>
    <input type="text" name="username" maxlength="20" placeholder="Username"><br>
    <input type="text" name="realname" maxlength="40" placeholder="Real name"><br>
    <input type="submit">
  </form>
  
  <form name="qa">
    <input type="hidden" name="lookfor" value="qa">
    Search for questions / answers
    <br>
    <input type="text" name="fromuser" maxlength="20" placeholder="From who">
    <img src="res/warning.svg" title="Unless you search for questions you 
    asked, only eponymously asked questions will show up"><br>
    <input type="text" name="touser" maxlength="20" placeholder="To who"><br>
    <input type="text" name="query" placeholder="Text to look for"><br>
    <input type="month" name="timeanswered"><br>
    <input type="submit">
  </form>
</aside>

<footer>
  <!--TODO-->
</footer>

</body>
</html>
