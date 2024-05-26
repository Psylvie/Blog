<?php
	
	namespace App\Controllers;
	
	use App\Repository\PostRepository;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	
	class PostController extends Controller
	{
		private PostRepository $postRepository;
		
		public function __construct()
		{
			parent::__construct();
			$this->postRepository = new PostRepository();
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
			
			
			$post = $this->postRepository->getPostById($id);
			if (!$post){
				$this->handleErrors();
				return;
			}
			$this->render('Posts/post.html.twig', ['post' => $post]);
			
		}
		
	}