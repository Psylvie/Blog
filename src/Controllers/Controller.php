<?php
	
	namespace App\Controllers;
	
	use App\Utils\Superglobals;
	use Twig\Environment;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	use Twig\Extension\DebugExtension;
	use Twig\Loader\FilesystemLoader;
	require_once __DIR__ . '/../Config/Recaptcha.php';
	
	class Controller {
		
		protected Environment $twig;
		
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
			$this->twig->addGlobal('session', $_SESSION);
		}
		
		/**
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
			echo $this->twig->render("$view", $data);
		}
		
		/**
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
	}
	