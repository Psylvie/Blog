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
            $this->redirect('/Blog/');
        }

        $userRole = Superglobals::getSession('user_role');
        $userId = Superglobals::getSession('user_id');
        $commentContent = htmlspecialchars_decode(trim(Superglobals::getPost('comment')));
        $postId = Superglobals::getPost('postId');

        if (!$userRole || ($userRole !== 'subscriber' && $userRole !== 'admin')) {
            Superglobals::setFlashMessage("danger", "Veuillez vous connecter pour soumettre un commentaire.");
            $this->redirect('/Blog/post/' . $postId);
        }
        try {
            $mysqlClient = DatabaseConnect::connect();
            $commentRepository = new CommentRepository($mysqlClient);
            $commentRepository->addComment($commentContent, $postId, $userId);
            Superglobals::setFlashMessage("info", "Votre commentaire a été soumis avec succès et est en attente de validation.");
            $this->redirect('/Blog/post/' . $postId);
        } catch (PDOException $e) {
            Superglobals::setFlashMessage("danger", "Une erreur s'est produite lors de la soumission de votre commentaire.");
            $this->redirect('/Blog/post/' . $postId);
        }
    }
}
	