<?php
	
	namespace App\Controllers\Admin;
	//	session_start();
	
	
	use App\Controllers\Controller;
	use App\Repository\CommentRepository;
	use App\Repository\PostRepository;
	use App\Repository\UserRepository;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	
	class AdminCommentController extends Controller
	{
		
		private CommentRepository $CommentRepository;
		private PostRepository $PostRepository;
		private UserRepository $UserRepository;
		
		public function __construct()
		{
			parent::__construct();
			$this->CommentRepository = new CommentRepository();
			$this->PostRepository = new PostRepository();
			$this->UserRepository = new UserRepository();
		}
		
		/**
		 * @throws RuntimeError
		 * @throws SyntaxError
		 * @throws LoaderError
		 * @throws \Exception
		 */
		public function showAllPosts()
		{
			$posts = $this->PostRepository->getAllPosts();
			$postsData = [];
			
			foreach ($posts as $post) {
				$postId = $post->getId();
				$comments = $this->CommentRepository->findAllByPostId($postId);
				
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
			$comments = $this->CommentRepository->findAllByPostId($postId);
			$post = $this->PostRepository->getPostById($postId);
			foreach ($comments as $comment) {
				$user = $this->CommentRepository->findUserByComment($comment->getId());
				$comment->user = $user;
			}
				$this->render('Admin/adminShowComments.html.twig', [
					'comments' => $comments,
					'post' => $post]);
			
		}
		
		public function handleCommentValidation()
		{
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$validationOption = $_POST['validationOption'] ?? null;
				$commentId = $_POST['commentId'] ?? null;
				$commentContent = $_POST['commentContent'] ?? null;
				if ($validationOption !== null && $commentId !== null) {
					switch ($validationOption) {
						case 'approved':
							$this->CommentRepository->updateCommentStatus($commentId, 'approved');
							break;
						case 'rejected':
							$this->CommentRepository->updateCommentStatus($commentId, 'rejected');
							break;
						default:
							$this->CommentRepository->updateCommentStatus($commentId, 'pending');
							break;
					}
					$postId = $_POST['postId'] ?? null;
					if ($postId !== null) {
						$redirectUrl = "/Blog/admin/showAllComments/$postId";
						$_SESSION['flash_message'] = 'Commentaire mis à jour avec succès !';
						$_SESSION['flash_type'] = 'success';
						header("Location: $redirectUrl");
						exit();
					}
					header('Location: /Blog/admin/showAllComments/{id}');
					
					
				}
			}
		}
		
	}