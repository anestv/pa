<?php namespace core;

class Model {
  
  protected static $_db;
  
  public function __construct(){
    if (!self::$_db) // if not already defined
      self::$_db = new \helpers\database();
  }
}
