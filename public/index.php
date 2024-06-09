<?php
require '../vendor/autoload.php';
session_start();
//header('X-Content-Type-Options: nosniff');
//header('X-Frame-Options: SAMEORIGIN');
//header('X-XSS-Protection: 1; mode=block');


use App\Controllers\Admin\AdminCommentController;
use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\AdminPostController;
use App\Controllers\Admin\AdminUserController;
use App\Controllers\CommentController;
use App\Controllers\Controller;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\PostController;
use App\Controllers\RegisterController;
use App\Controllers\UserController;
use App\Router;
use App\Utils\Superglobals;

$uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

$userRole = filter_var(Superglobals::getSession('user_role') ?? null);

$router = new Router();
$router->addRoute('/Blog/', HomeController::class, 'homePage');
$router->addRoute('/Blog/privacyPolicy', HomeController::class, 'privacyPolicy');
$router->addRoute('/Blog/legalMention', HomeController::class, 'legalMention');
$router->addRoute('/Blog/contact', HomeController::class, 'contact');
$router->addRoute('/Blog/inscription', RegisterController::class, 'registrationForm');
$router->addRoute('/Blog/register', RegisterController::class, 'register');
$router->addRoute('/Blog/user/updateProfile/{id}', UserController::class, 'updateProfile');
$router->addRoute('/Blog/login', LoginController::class, 'loginForm');
$router->addRoute('/Blog/loginProcess', LoginController::class, 'login');
$router->addRoute('/Blog/logout', LoginController::class, 'logout');
$router->addRoute('/Blog/user/{id}', UserController::class, 'show');
$router->addRoute('/Blog/user/deleteUser/{id}', UserController::class, 'deleteUser');
$router->addRoute('/Blog/showPassword', LoginController::class, 'showPasswordResetForm');
$router->addRoute('/Blog/reset-password', LoginController::class, 'resetPassword');
$router->addRoute('/Blog/reset', LoginController::class, 'requestPasswordReset');
$router->addRoute('/Blog/newPassword/{token}', LoginController::class, 'newPassword');
$router->addRoute('/Blog/first-connection', LoginController::class, 'firstConnectionForm');
$router->addRoute('/Blog/first-connection-process', LoginController::class, 'handleFirstConnection');
$router->addRoute('/Blog/posts', PostController::class, 'list');
$router->addRoute('/Blog/post/{id}', PostController::class, 'show');
$router->addRoute('/Blog/contactForm', HomeController::class, 'contactForm');
$router->addRoute('/Blog/addComment', CommentController::class, 'addComment');
$router->addRoute('/Blog/Error/', Controller::class, 'handleErrors');


if (isset($userRole) && $userRole == 'admin') {
    $router->addRoute('/Blog/admin', AdminController::class, 'index');
    $router->addRoute('/Blog/admin/showPost', AdminPostController::class, 'showPost');
    $router->addRoute('/Blog/admin/newPost', AdminPostController::class, 'newPost');
    $router->addRoute('/Blog/admin/createPost', AdminPostController::class, 'createPost');
    $router->addRoute('/Blog/admin/updatePost/{id}', AdminPostController::class, 'updatePost');
    $router->addRoute('/Blog/admin/deletePost/{id}', AdminPostController::class, 'deletePost');
    $router->addRoute('/Blog/admin/showAllPosts', AdminCommentController::class, 'showAllPosts');
    $router->addRoute('/Blog/admin/showAllComments/{id}', AdminCommentController::class, 'showAllComments');
    $router->addRoute('/Blog/admin/handleCommentValidation', AdminCommentController::class, 'handleCommentValidation');
    $router->addRoute('/Blog/admin/users/list', AdminUserController::class, 'list');
    $router->addRoute('/Blog/admin/users/show/{id}', AdminUserController::class, 'show');
    $router->addRoute('/Blog/admin/users/delete/{id}', AdminUserController::class, 'delete');
    $router->addRoute('/Blog/admin/users/update/{id}', AdminUserController::class, 'update');
    $router->addRoute('/Blog/admin/users/create', AdminUserController::class, 'createUser');
    $router->addRoute('/Blog/admin/users/createUser', AdminUserController::class, 'createUserProcess');
}

try {
    $router->dispatch($uri);
} catch (\Exception $e) {
    if (str_starts_with($uri, '/Blog/')) {
        Controller::redirect('/Blog/Error/');
    }
    error_log('Error: '. $e->getMessage());
}
