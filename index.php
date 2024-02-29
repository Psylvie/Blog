<?php
	
	require_once __DIR__ . '/vendor/autoload.php';
	
	use App\Config\DatabaseConnect;
	use App\Router;
	
	
	try {
		$mysqlClient = DatabaseConnect::connect();
		
	}catch (\Exception $e) {
		echo 'Erreur de connexion a la base de données !: ' . $e->getMessage();
	}
	
	
	$uri = $_SERVER['REQUEST_URI'];

	$router = new Router();

//	$router->addRoute('/blog/', \App\Controllers\HomeController::class, 'homePage');

	try {
		$router->dispatch($uri);
	} catch (\Exception $e) {
		echo 'Error: ' . $e->getMessage();
	}
?>

