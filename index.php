<?php
	
	require_once __DIR__ . '/vendor/autoload.php';
	
	use App\Router;
	
	$uri = $_SERVER['REQUEST_URI'];
	
	$router = new Router();
	
	$router->addRoute('/blog/', 'App\Controllers\UserController', 'index');
	
	try {
		$router->dispatch($uri);
	} catch (\Exception $e) {
		echo 'Error: ' . $e->getMessage();
	}
