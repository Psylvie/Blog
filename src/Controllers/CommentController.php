<?php
	namespace App\Controllers;
	
	use App\Config\DatabaseConnect;
	use App\Repository\CommentRepository;
	use PDOException;
	
	
	class CommentController extends HomeController
	{
		public function addComment(): void
		{
			$userRole = $this->getSessionData('user_role');
			$userId = $this->getSessionData('user_id');
			if (!$userRole || $userRole !== 'subscriber' && $userRole !== 'admin') {
				$this->setFlashMessage("danger", "Veuillez vous connecter pour soumettre un commentaire.");
				header('Location: /Blog/post/' . $_POST['postId']);
				exit();
			}
			$commentContent = $_POST['comment'];
			$postId = $_POST['postId'];
			try {
				$mysqlClient = DatabaseConnect::connect();
				$commentRepository = new CommentRepository($mysqlClient);
				$commentRepository->addComment($commentContent, $postId, $userId);
				$this->setFlashMessage("info", "Votre commentaire a été soumis avec succès et est en attente de validation.");
				header('Location: /Blog/post/' . $postId);
				exit();
			} catch (PDOException $e) {
				header('Location: /Blog/post/' . $postId);
				exit();
			}
		}
	}
	