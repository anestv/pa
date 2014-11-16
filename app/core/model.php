<?php namespace core;

class Model extends Controller {

	protected static $_db;
	
	public function __construct(){
		self::$_db = $this->_db = new \helpers\database();
	}
}
