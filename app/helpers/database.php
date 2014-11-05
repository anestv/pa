<?php namespace helpers;
use \mysqli;
class Database extends mysqli{

	function __construct(){

		try {
			parent::__construct(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		} catch(Exception $e){
			Logger::newMessage($e);
			logger::customErrorMsg();
		}

	}
}
