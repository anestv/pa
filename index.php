<?php
if(file_exists('vendor/autoload.php')){
	require 'vendor/autoload.php';
} else {
	echo "<h1>Please install via composer.json</h1>";
	echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
	echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
	exit;
}

//create alias for Router
use \core\router as Router;
use \helpers\url as Url;

//define routes
Router::any('', '\controllers\index@index');
Router::get('login', '\controllers\login@get');
Router::post('login', '\controllers\login@post');
Router::any('logout', '\controllers\login@logout');
Router::any('terms', function(){
  \core\view::render('terms');
});

//if no route found
Router::error('\core\error@index');

//execute matched routes
Router::dispatch();