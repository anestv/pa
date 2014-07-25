<!DOCTYPE html>
<html>
<head>
<title>Search - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="general.css">
</head>
<body>


<div id="topNav">
  <!-- top nav bar -->
</div>

<!-- results -->
<main>
  
<?php
//an ontws psaxnei kati
if (!empty($_GET['lookfor'])){
  $subj = $_GET['lookfor'];
  
  switch ($subj) {
    case 'u':
      ;
      break;
    
    case 'qa':
      ;
      break;
    
    default:
      terminate('Invalid search terms',400);
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
