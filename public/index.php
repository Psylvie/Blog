<?php
	error_reporting(E_ALL);
	ini_set('display_errors', true);
	session_start();
	print_r($_SESSION);
	
	session_start();
	print_r($_SESSION);
	require __DIR__ . '/../vendor/autoload.php';
	
	use App\Controllers\Admin\AdminController;
	use App\Controllers\Admin\AdminPostController;
	use App\Controllers\CommentController;
	use App\Controllers\HomeController;
	use App\Controllers\PostController;
	use App\Router;
	
	$uri = $_SERVER['REQUEST_URI'];

	$router = new Router();
	$router->addRoute('/Blog/admin', AdminController::class, 'index');
	$router->addRoute('/Blog/admin/showPost', AdminPostController::class, 'showPost');
	$router->addRoute('/Blog/admin/newPost', AdminPostController::class, 'newPost');
	$router->addRoute('/Blog/admin/createPost', AdminPostController::class, 'createPost');
	$router->addRoute('/Blog/admin/deletePost/{id}', AdminPostController::class, 'deletePost');
	$router->addRoute('/Blog/', HomeController::class, 'homePage');
	$router->addRoute('/Blog/posts', PostController::class, 'list');
	$router->addRoute('/Blog/post/{id}', PostController::class, 'show');
	$router->addRoute('/Blog/contactForm', HomeController::class, 'contactForm');
	$router->addRoute('/Blog/addComment', CommentController::class, 'addComment');
	try {
		$router->dispatch($uri);
	} catch (\Exception $e) {
		echo 'Error: ' . $e->getMessage();
	}


