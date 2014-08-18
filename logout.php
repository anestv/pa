<?php 

if (session_destroy()){
  header("Location: login.php?loggedOut=1");
  echo "You have been successfully logged out";
} else terminate("The current session could not be destroyed", 500);

?>
