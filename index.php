<?php
	session_start();
	require_once __DIR__ . '/vendor/autoload.php';
	
	use App\Config\DatabaseConnect;
	use App\Controllers\HomeController;
	use App\Controllers\PostController;
	use App\Router;
	
	
	try {
		$mysqlClient = DatabaseConnect::connect();
		
	}catch (\Exception $e) {
		echo 'Erreur de connexion a la base de données !: ' . $e->getMessage();
	}
	
	$uri = $_SERVER['REQUEST_URI'];

	$router = new Router();
	
	$router->addRoute('/Blog/', HomeController::class, 'homePage');
	$router->addRoute('/Blog/posts', PostController::class, 'list');
	$router->addRoute('/Blog/post/{id}', PostController::class, 'show');
	$router->addRoute('/Blog/contactForm', HomeController::class, 'contactForm');

	try {
		$router->dispatch($uri);
	} catch (\Exception $e) {
		echo 'Error: ' . $e->getMessage();
	}


