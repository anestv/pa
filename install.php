<?php

define('INSTALLATION_FLAG_FILE', __DIR__. '/.already_installed');

class Install extends \core\model {
  
  public function __construct($filename = 'privateask.sql'){
    parent::__construct();
    
    
    if (!file_exists($filename))
      die("ERROR: The initialisation file ($filename) doesn't exist");
    
    $sql = file_get_contents($filename);
    
    $res = self::$_db->multi_query($sql);
    
    if ($res){
      echo "Successfully ran the db initialisation file($filename)";
      
      // create this file so it does not run again
      touch(INSTALLATION_FLAG_FILE);
      
    } else
      echo "There was an error initialising the db. ". self::$_db->error;
  }
}

if (!file_exists(INSTALLATION_FLAG_FILE)) new Install();
