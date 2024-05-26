<?php
	
	namespace App\Controllers\Admin;
	
	use App\Controllers\Controller;
	use App\Repository\CommentRepository;
	use App\Repository\PostRepository;
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
							$this->commentRepository->updateCommentStatus($commentId, 'approved');
							break;
						case 'rejected':
							$this->commentRepository->updateCommentStatus($commentId, 'rejected');
							break;
						default:
							$this->commentRepository->updateCommentStatus($commentId, 'pending');
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
	