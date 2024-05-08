<?php
	
	namespace App\Controllers\Admin;
	
	use App\Controllers\Controller;
	use App\Repository\PostRepository;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	
	class AdminController extends Controller {
		
		private PostRepository $postRepository;
		
		public function __construct()
		{
			parent::__construct();
			$this->postRepository = new PostRepository();
			
		}
		
		/**
		 * @throws RuntimeError
		 * @throws SyntaxError
		 * @throws LoaderError
		 * @throws \Exception
		 */
		public function index()
		
		{
			$posts = $this->postRepository->findLatestPosts(3);
			$this->render('Admin/adminHome.html.twig', ['posts' => $posts]);
		}
		
		
	}