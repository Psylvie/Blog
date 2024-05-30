<?php
	
	namespace App\Controllers;
	
	use App\Repository\CommentRepository;
	use App\Repository\PostRepository;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	
	class PostController extends Controller
	{
		private PostRepository $postRepository;
		private CommentRepository $commentRepository;
		
		public function __construct()
		{
			parent::__construct();
			$this->postRepository = new PostRepository();
			$this->commentRepository = new CommentRepository();
		}
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 * @throws \Exception
		 */
		public function list(){
			$posts = $this->postRepository->getAllPosts();
			$this->render('Posts/listPosts.html.twig', ['posts' => $posts]);
			
		}
		
		/**
		 * @throws RuntimeError
		 * @throws SyntaxError
		 * @throws LoaderError
		 * @throws \Exception
		 */
		public function show($id){
			$comments = $this->commentRepository->findAllByPostId($id);
			$post = $this->postRepository->getPostById($id);
			foreach ($comments as $comment) {
				$user = $this->commentRepository->findUserByComment($comment->getId());
				$comment->user = $user;
			}
			if (!$post){
				$this->handleErrors();
				return;
			}
			$this->render('Posts/post.html.twig', [
				'post' => $post,
				'comments' => $comments
			]);
			
		}
		
	}