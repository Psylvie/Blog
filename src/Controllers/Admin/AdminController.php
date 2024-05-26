<?php
	
	namespace App\Controllers\Admin;
	
	use App\Controllers\Controller;
	use App\Repository\CommentRepository;
	use App\Repository\PostRepository;
	use App\Repository\UserRepository;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	
	class AdminController extends Controller {
		
		private PostRepository $postRepository;
		private UserRepository $userRepository;
		
		public function __construct()
		{
			parent::__construct();
			$this->postRepository = new PostRepository();
			$this->userRepository = new UserRepository();
			
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
			$users = $this->userRepository->findLatestUsers(3);
			$this->render('Admin/adminHome.html.twig', ['posts' => $posts, 'users' => $users]);
			
			
		}
		
		
	}