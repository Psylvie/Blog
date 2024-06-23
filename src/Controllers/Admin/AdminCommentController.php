<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Utils\Superglobals;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AdminCommentController
 * @package App\Controllers\Admin
 */
class AdminCommentController extends Controller
{
    /**
     * AdminCommentController constructor.
     */
    public function __construct()
    {
        parent::__construct();

    }


    /**
     * show all posts
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function showAllPosts()
    {
        $posts = $this->postRepository->getAllPosts();
        $postsData = [];

        foreach ($posts as $post) {
            $postId = $post->getId();
            $comments = $this->commentRepository->findAllByPostId($postId);

            $postsData[] = [
                'post' => $post,
                'comments' => $comments ?? []
            ];
        }
        $this->render('/Admin/adminShowAllCommentsPosts.html.twig', ['postsData' => $postsData]);
    }

    /**
     * show all comments for a post
     * @param $postId
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function showAllComments($postId)
    {
        $commentsUsers = [];
        $comments = $this->commentRepository->findAllByPostId($postId);
        $post = $this->postRepository->getPostById($postId);
        foreach ($comments as $comment) {
            $user = $this->commentRepository->findUserByComment($comment->getId());
            $commentsUsers[$comment->getId()] = $user;
        }
        $this->render('Admin/adminShowComments.html.twig', [
            'commentsUsers' => $commentsUsers,
            'comments' => $comments,
            'post' => $post]);
    }

    /**
     * show pending comments
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function showPendingComments()
    {
        $comments = $this->commentRepository->findAllPendingComments();
        $commentsData = [];

        foreach ($comments as $comment) {
            $user = $this->commentRepository->findUserByComment($comment->getId());
            $post = $this->postRepository->getPostById($comment->getPostId());

            $commentsData[] = [
                'comment' => $comment,
                'user' => $user,
                'postTitle' => $post->getTitle(),
                'postChapo' => $post->getChapo(),
            ];
        }

        $this->render('Admin/adminCommentsPending.html.twig', [
            'commentsData' => $commentsData
        ]);
    }

    /**
     * handle comment validation (approve, reject, pending)
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError|Exception
     */
    public function handleCommentValidation()
    {
        $requestMethod = Superglobals::getServer('REQUEST_METHOD');

        if ($requestMethod === 'POST') {
            $validationOption = Superglobals::getPost('validationOption');
            $commentId = Superglobals::getPost('commentId');
            $referer = Superglobals::getServer('HTTP_REFERER');
            $commentContent = Superglobals::getPost('commentContent');
            $postId = Superglobals::getPost('postId');

            if ($validationOption !== null && $commentId !== null) {
                switch ($validationOption) {
                    case 'approved':
                        $this->commentRepository->updateCommentStatus($commentId, 'approved');
                        break;
                    case 'rejected':
                        $this->commentRepository->updateCommentStatus($commentId, 'rejected');
                        break;
                    default:
                        $this->commentRepository->updateCommentStatus($commentId, 'pending');
                        break;
                }

                if (!empty($referer)) {
                    $this->redirect($referer);
                } elseif ($postId !== null) {
                    $redirectUrl = "/Blog/admin/showAllComments/$postId";
                    Superglobals::setFlashMessage('success', 'Commentaire mis à jour avec succès !');
                    $this->redirect($redirectUrl);
                }
            }
        }
    }
}
