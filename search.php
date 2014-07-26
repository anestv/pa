<!DOCTYPE html>
<html>
<head>
<title>Search - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
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
  while ($row = mysqli_fetch_array($res))
    echo '<tr><td>'.$row['username'].'</td><td>'.$row['realname'].'</td></tr>';
  ?>
  
</tbody>
</table>
  <?php 
  
}

function doQASearch() {
  
}




//an ontws psaxnei kati
if (!empty($_GET['lookfor'])){
  $subj = $_GET['lookfor'];
  
  switch ($subj) {
    case 'u':  doUserSearch(); break;
    case 'qa': doQASearch(); break;
    default:   terminate('Invalid search terms',400);
  }
}
?>

</main>

<!-- bar with search options -->
<aside>
  
</aside>

<footer>
  <!--TODO-->
</footer>

</body>
</html>
