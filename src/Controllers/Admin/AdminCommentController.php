<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Utils\Superglobals;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminCommentController extends Controller
{

    private CommentRepository $commentRepository;
    private PostRepository $postRepository;

    public function __construct()
    {
        parent::__construct();
        $this->commentRepository = new CommentRepository();
        $this->postRepository = new PostRepository();
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws \Exception
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
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws \Exception
     */
    public function showAllComments($postId)
    {
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
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function handleCommentValidation()
    {
        $requestMethod = Superglobals::getServer('REQUEST_METHOD');

        if ($requestMethod === 'POST') {
            $validationOption = Superglobals::getPost('validationOption');
            $commentId = Superglobals::getPost('commentId');
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

                if ($postId !== null) {
                    $redirectUrl = "/Blog/admin/showAllComments/$postId";
                    Superglobals::setFlashMessage('success', 'Commentaire mis à jour avec succès !');
                    $this->redirect($redirectUrl);
                }
            }
        }
    }
}
	