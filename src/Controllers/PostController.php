<?php

namespace App\Controllers;

use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PostController
 * @package App\Controllers
 */
class PostController extends Controller
{
    /**
     * PostController constructor.
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Display the list of posts
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function list()
    {
        $posts = $this->postRepository->getAllPosts();
        $this->render('Posts/listPosts.html.twig', ['posts' => $posts]);
    }

    /**
     * show post details
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function show($id)
    {
        $comments = $this->commentRepository->findAllByPostId($id);
        $post = $this->postRepository->getPostById($id);
        foreach ($comments as $comment) {
            $user = $this->commentRepository->findUserByComment($comment->getId());
            $comment->user = $user;
        }
        if (!$post) {
            $this->handleErrors();
            return;
        }
        $this->render('Posts/post.html.twig', [
            'post' => $post,
            'comments' => $comments
        ]);

    }

}