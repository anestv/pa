<?php namespace core;

class Error extends controller {

	public function index(){
		http_response_code(404);
		
        self::errorMessage('Page not found');
	}

}
