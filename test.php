<?php

  $a = openssl_random_pseudo_bytes(10);
  $hexrand = bin2hex(openssl_random_pseudo_bytes(10));
  $alataki = base_convert($hexrand, 16, 30) . $_POST['rand'];
  
  $d = $c. 'hbowe 8gvpscelfyuc 6ujc ';
  
  $pass = '123456';
  
  $cr_arr = array('salt'=> $alataki, 'cost'=> 10);
  $hspass = password_hash($pass, PASSWORD_DEFAULT, $cr_arr);
  
  
  
  
  
  
  
  
  var_dump($a);echo '<br>';
  var_dump($b);echo '<br>';
  var_dump($c);echo '<br>';
  var_dump($d);echo '<br>';
  var_dump($hspass);echo '<br>';
  
?>
GOOD CODE HERE AND BELOW