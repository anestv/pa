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

// initialise the config object
// originally was on \core\Controller::__contruct()
new \core\config();

//define routes
Router::any('', '\controllers\index@index');
Router::get('login', '\controllers\login@get');
Router::post('login', '\controllers\login@post');
Router::any('logout', '\controllers\login@logout');
Router::get('register', '\controllers\register@get');
Router::post('register', '\controllers\register@post');
Router::get('question/(:num)', '\controllers\question@view');
Router::get ('question/(:num)/report', '\controllers\question@getReport');
Router::post('question/(:num)/report', '\controllers\question@postReport');
Router::get ('question/(:num)/delete', '\controllers\question@getDelete');
Router::post('question/(:num)/delete', '\controllers\question@postDelete');
Router::get ('question/(:num)/answer', '\controllers\answer@get');
Router::post('question/(:num)/answer', '\controllers\answer@post');
Router::any('terms', '\controllers\statics@terms');
Router::any('help', '\controllers\statics@help');
Router::get('user/(:user)', '\controllers\profile@profile');
Router::get('api/profileDisplay/(:user)', '\controllers\api@profileDisplay');
Router::get('api/load/(:user)', '\controllers\api@load');
Router::post('api/friends', '\controllers\api@friends');
Router::post('api/ask', '\controllers\api@ask');
Router::get('pending', '\controllers\pending@get');

//if no route found
Router::error('\core\error@index');

$GLOBALS['user'] = new \models\User(\models\User::CURRENT);

//execute matched routes
Router::dispatch();