<?php

$needInstall = true;

class Install extends \core\model {
  
  public function __construct($filename = 'privateask.sql'){
    parent::__construct();
    
    
    if (!file_exists($filename))
      die("ERROR: The initialisation file ($filename) doesn't exist");
    
    $sql = file_get_contents($filename);
    
    $res = self::$_db->multi_query($sql);
    
    if ($res){
      echo "Successfully ran the db initialisation file($filename)";
      
      // change this file so it does not run again
      $thisFile = file(__FILE__);
      $thisFile[2] = '$needInstall = false;'. PHP_EOL;
      file_put_contents(__FILE__, $thisFile);
      
    } else
      echo "There was an error initialising the db. ". self::$_db->error;
  }
}

if ($needInstall) new Install();
