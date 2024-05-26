<?php
	namespace App\Controllers;
	
	use App\Config\DatabaseConnect;
	use App\Repository\CommentRepository;
	use PDOException;
	
	
	class CommentController extends HomeController
	{
		public function addComment(): void
		{
			if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'subscriber' && $_SESSION['user_role'] !== 'admin')) {
				$_SESSION['flash_message'] = "Veuillez vous connecter pour soumettre un commentaire.";
				$_SESSION['flash_type'] = "danger";
				header('Location: /Blog/post/' . $_POST['postId']);
				exit();
			}
			
			$commentContent = $_POST['comment'];
			$postId = $_POST['postId'];
			$userId = $_SESSION['user_id'];
			
			try {
				$mysqlClient = DatabaseConnect::connect();
				$commentRepository = new CommentRepository($mysqlClient);
				$commentRepository->addComment($commentContent, $postId, $userId);
				$_SESSION['flash_message'] = "Votre commentaire est en attente de validation.";
				$_SESSION['flash_type'] = "info";
				header('Location: /Blog/post/' . $postId);
				exit();
			} catch (PDOException $e) {
				header('Location: /Blog/post/' . $postId);
				exit();
			}
		}
	}
	