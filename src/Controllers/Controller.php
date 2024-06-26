<?php

namespace App\Controllers;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Utils\Superglobals;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../Config/Recaptcha.php';

/**
 * Class Controller
 * @package App\Controllers
 */
class Controller
{
    protected Environment $twig;
    protected PostRepository $postRepository;
    protected UserRepository $userRepository;
    protected CommentRepository $commentRepository;

    /**
     * Controller constructor.
     */
    public function __construct()
    {

        $loader = new FilesystemLoader(__DIR__ . '\..\Views');
        $this->twig = new Environment($loader, [
            'debug' => true,
            'cache' => false,
        ]);
        $this->twig->addExtension(new DebugExtension());
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $sessionData = Superglobals::getSessionData();
        $this->twig->addGlobal('session', $sessionData);

        $this->postRepository = new PostRepository();
        $this->userRepository = new UserRepository();
        $this->commentRepository = new CommentRepository();
    }

    /**
     * render a view
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    protected function render($view, $data = []): void
    {
        $data['flash_message'] = Superglobals::getSession('flash_message');
        $data['flash_type'] = Superglobals::getSession('flash_type');
        Superglobals::unsetSession('flash_message');
        Superglobals::unsetSession('flash_type');

        ob_start();
        echo $this->twig->render("$view", $data);
        ob_end_flush();
    }

    /**
     * redirect to a path
     * @param $path
     */
    public static function redirect($path): void
    {
        header("Location: $path");
    }

    /**
     * handle errors and display error page
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function handleErrors(): void
    {
        $userRole = Superglobals::getSession('user_role');
        $isAdmin = ($userRole === 'admin');
        $this->render('Error/error.html.twig', [
            'is_admin' => $isAdmin,
            'recaptchaSiteKey' => RECAPTCHA_SITE_KEY]);
    }

    public function testInput($data): string
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}
	