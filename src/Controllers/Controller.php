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
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 */
		public function handleErrors(): void
		{
			$isAdmin = false;
			if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
				$isAdmin = true;
			}
			$this->render('Error/error.html.twig', ['is_admin' => $isAdmin]);
		}
		
		public function getSessionData($key, $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS)
		{
			$data = $_SESSION[$key] ?? null;
			return filter_var($data, $filter);
		}
		public function setSessionData($key, $value): void
		{
			$_SESSION[$key] = $value;
		}
		public function setFlashMessage($type, $message): void
		{
			$_SESSION['flash_type'] = $type;
			$_SESSION['flash_message'] = $message;
		}
		
		public function getFlashMessage(): array
		{
			$flash = [];
			if (isset($_SESSION['flash_type']) && isset($_SESSION['flash_message'])) {
				$flash['type'] = $_SESSION['flash_type'];
				$flash['message'] = $_SESSION['flash_message'];
				unset($_SESSION['flash_type'], $_SESSION['flash_message']);
			}
			return $flash;
		}
	}
	