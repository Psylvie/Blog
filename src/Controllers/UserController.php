<?php
	
	namespace App\Controllers;
	
	use App\Controller;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	
	class UserController extends Controller {
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 */
		public function index()
		{
			$users = [
				['name' => 'John Doe'],
				['name' => 'Jane Doe'],
				['name' => 'Jim Doe'],
			];
			
			$this->render('Users/index.twig', compact('users'));
		}
	}
	
	