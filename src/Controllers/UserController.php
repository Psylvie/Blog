<?php
	
	namespace App\Controllers;
	
	use App\Config\DatabaseConnect;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	
	class UserController extends Controller
	{

		
		public function index()
		{
			try {
				$mysqlClient = DatabaseConnect::connect();
				
				$users = $mysqlClient->query('SELECT * FROM users ORDER BY createdAt DESC ') -> fetchAll();
				
				$this->render('Users/index.twig', ['users' => $users]);
			}catch (\Exception $e) {
				echo 'Erreur de connexion a la base de données !: ' . $e->getMessage();
			}
		}
		
	}
	
	