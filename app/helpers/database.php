<?php namespace helpers;
use \mysqli;
class Database extends mysqli{
  
  function __construct(){
    
    parent::__construct(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
  }
}
