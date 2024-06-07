<?php
	namespace App\Controllers;
	
	use App\Config\DatabaseConnect;
	use App\Repository\CommentRepository;
	use App\Utils\Superglobals;
	use PDOException;
	
	
	class CommentController extends HomeController
	{
		public function addComment(): void
		{
			if (Superglobals::getServer('REQUEST_METHOD') !== 'POST') {
				header('Location: /Blog/');
				exit();
			}
			
			$userRole = Superglobals::getSession('user_role');
			$userId = Superglobals::getSession('user_id');
			$commentContent = htmlspecialchars(trim(Superglobals::getPost('comment')));
			$postId = Superglobals::getPost('postId');
			
			if (!$userRole || ($userRole !== 'subscriber' && $userRole !== 'admin')) {
				Superglobals::setFlashMessage("danger", "Veuillez vous connecter pour soumettre un commentaire.");
				header('Location: /Blog/post/' . $postId);
				exit();
			}
			try {
				$mysqlClient = DatabaseConnect::connect();
				$commentRepository = new CommentRepository($mysqlClient);
				$commentRepository->addComment($commentContent, $postId, $userId);
				Superglobals::setFlashMessage("info", "Votre commentaire a été soumis avec succès et est en attente de validation.");
				header('Location: /Blog/post/' . $postId);
				exit();
			} catch (PDOException $e) {
				Superglobals::setFlashMessage("danger", "Une erreur s'est produite lors de la soumission de votre commentaire.");
				header('Location: /Blog/post/' . $postId);
				exit();
			}
		}
	}
	