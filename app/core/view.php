<?php namespace core;
use helpers\session as Session;

class View {

	public static function render($path,$data = false, $error = false){
		require "app/views/$path.php";
	}

	public static function rendertemplate($path,$data = false){
		require "app/templates/".Session::get('template')."/$path.php";
	}
	
}