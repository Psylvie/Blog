<?php
	error_reporting(E_ALL);
	ini_set('display_errors', true);
	session_start();
	print_r($_SESSION);
	require __DIR__ . '/../vendor/autoload.php';
	
	use App\Controllers\Admin\AdminController;
	use App\Controllers\Admin\AdminPostController;
	use App\Controllers\CommentController;
	use App\Controllers\HomeController;
	use App\Controllers\LoginController;
	use App\Controllers\PostController;
	use App\Controllers\RegisterController;
	use App\Controllers\UserController;
	use App\Router;
	
	$uri = $_SERVER['REQUEST_URI'];

	$router = new Router();
	$router->addRoute('/Blog/admin', AdminController::class, 'index');
	$router->addRoute('/Blog/admin/showPost', AdminPostController::class, 'showPost');
	$router->addRoute('/Blog/admin/newPost', AdminPostController::class, 'newPost');
	$router->addRoute('/Blog/admin/createPost', AdminPostController::class, 'createPost');
	$router->addRoute('/Blog/admin/updatePost/{id}', AdminPostController::class, 'updatePost');
	$router->addRoute('/Blog/admin/deletePost/{id}', AdminPostController::class, 'deletePost');
	$router->addRoute('/Blog/', HomeController::class, 'homePage');
	$router->addRoute('/Blog/inscription', RegisterController::class, 'registrationForm');
	$router->addRoute('/Blog/register', RegisterController::class, 'register');
	$router->addRoute('/Blog/user/updateProfile/{id}', UserController::class, 'updateProfile');
	$router->addRoute('/Blog/login', LoginController::class, 'loginForm');
	$router->addRoute('/Blog/loginProcess', LoginController::class, 'login');
	$router->addRoute('/Blog/logout', LoginController::class, 'logout');
	$router->addRoute('/Blog/user/{id}', UserController::class, 'show');
	$router->addRoute('/Blog/user/deleteUser/{id}', UserController::class, 'deleteUser');
	$router->addRoute('/Blog/reset-password', LoginController::class, 'resetPassword');
	$router->addRoute('/Blog/reset', LoginController::class, 'requestPasswordReset');
	$router->addRoute('/Blog/newPassword/{token}', LoginController::class, 'newPassword');
	$router->addRoute('/Blog/posts', PostController::class, 'list');
	$router->addRoute('/Blog/post/{id}', PostController::class, 'show');
	$router->addRoute('/Blog/contactForm', HomeController::class, 'contactForm');
	$router->addRoute('/Blog/addComment', CommentController::class, 'addComment');
	try {
		$router->dispatch($uri);
	} catch (\Exception $e) {
		echo 'Error: ' . $e->getMessage();
	}


