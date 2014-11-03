<?php namespace core;
use core\config as Config;
use core\view as View;
use core\error as Error;

class Controller {

	public $view;

	public function __construct(){	
		//initialise the config object
		new config();
		//initialise the views object
		$this->view = new view();
	}

	//Display an error page if nothing exists
	protected function _error($error) {
		require 'app/core/error.php';
		$this->_controller = new error($error);
	    	$this->_controller->index();
	    	die;
	}
  
  static function handleException($e, $header = 'Oops, something went wrong.'){
    if ($e instanceof RuntimeException)
      http_response_code(500);
    else http_response_code(400);
    
    $excMsg = $e->getMessage();
    header("X-Error-Descr: $excMsg");
    echo '<div class="center480 ui warning message"><div class="header">';
    echo "$header</div><p>$excMsg</p></div>";
  }

}
