<?php namespace core;

class Model extends Controller {

	protected $_db;
	
	public function __construct(){
		$this->_db = new \helpers\database();
	}
}
