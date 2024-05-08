<?php
	
	namespace App\Controllers;
	
	use Twig\Environment;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	use Twig\Extension\DebugExtension;
	use Twig\Loader\FilesystemLoader;
	
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
			$data['flash_message'] = $_SESSION['flash_message'] ?? null;
			$data['flash_type'] = $_SESSION['flash_type'] ?? null;
			unset($_SESSION['flash_message']);
			unset($_SESSION['flash_type']);
			echo $this->twig->render("$view", $data);
			
		}
	}
	