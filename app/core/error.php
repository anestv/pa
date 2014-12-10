<?php namespace Core;
use core\controller as Controller;
use core\view as View;

class Error extends Controller {

	private $_error = null; 

	public function __construct($error){
		parent::__construct();
		$this->_error = $error;
	}

	public function index(){
		http_response_code(404);
		
        self::errorMessage('Page not found');
	}

}
