<?php
	
	namespace App\Controllers;
	
	use Twig\Environment;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	use Twig\Loader\FilesystemLoader;
	
	class Controller {
		
		protected Environment $twig;
		
		public function __construct()
		{
			$loader = new FilesystemLoader(__DIR__ . '\..\Views');
			$this->twig = new Environment($loader, [
				'cache' => false,
			]);
		}
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 */
		protected function render($view, $data = []): void
		{
			echo $this->twig->render("$view", $data);
		
		}
	}
	
	