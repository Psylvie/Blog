<?php
	namespace App\Controllers\Admin;
	
	use App\Controllers\Controller;
	use App\Repository\PostRepository;
	use PDOException;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	include __DIR__ . '/../../config/Config.php';
	
	
	class AdminPostController extends Controller
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
		public function showPost()
		{
			$this->postRepository = new PostRepository();
			$posts = $this->postRepository->getAllPosts();
			$this->render('Admin/adminShowPost.html.twig', ['posts' => $posts]);
		}
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 */
		public function newPost()
		{
			$this->render('Admin/adminFormPost.html.twig');
		}
		
		/**
		 * @throws \Exception
		 */
		public function createPost()
		{
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$title = isset($_POST['title']) ? trim($_POST['title']) : '';
				$chapo = isset($_POST['chapo']) ? trim($_POST['chapo']) : '';
				$author = isset($_POST['author']) ? trim($_POST['author']) : '';
				$content = isset($_POST['content']) ? trim($_POST['content']) : '';
				$published = isset($_POST['published']) ? intval($_POST['published']) : 1;
				$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
				if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
					$allowed = ['jpg' => 'image/jpeg',
						'jpeg' => 'image/jpeg',
						'png' => 'image/png'
					];
					$filename = $_FILES['image']['name'];
					$filetype = $_FILES['image']['type'];
					$filesize = $_FILES['image']['size'];
					
					$extension = pathinfo($filename, PATHINFO_EXTENSION);
					if (!array_key_exists($extension, $allowed) || !in_array($filetype, $allowed)) {
						die("erreur de type de fichier");
					}
					if ($filesize > 1024 * 1024) {
						die("erreur de taille de fichier");
					}
					$newname = md5(uniqid());
					$newfilename = UPLOADS_POST_PATH . $newname . '.' . $extension;
					move_uploaded_file($_FILES['image']['tmp_name'], $newfilename);
				}
				try {
					$postRepository = new PostRepository();
					$postRepository->createPost($title, $chapo, $author, $content, $newname . '.' . $extension, $userId, $published);
					$_SESSION['flash_message'] = "Le post a été créé avec succès !";
					$_SESSION['flash_type'] = "success";
					header('Location: /Blog/admin/newPost');
					exit;
				} catch (PDOException $e) {
					$_SESSION['flash_message'] = "Erreur de connexion à la base de données : " . $e->getMessage();
					$_SESSION['flash_type'] = "danger";
					header('Location: /Blog/admin');
					exit;
				}
			} else {
				header('Location: /Blog/admin/newPost');
				exit;
			}
		}
		
		/**
		 * @throws \Exception
		 */
		public function deletePost($postId):void
		{
			try {
				$post = $this->postRepository->getPostById($postId);
				$imageName = $post?->getImage();
				$postRepository = new PostRepository();
				$postRepository->deletePost($postId);
				if ($imageName !== null) {
					unlink(UPLOADS_POST_PATH . $imageName);
				}
				$_SESSION['flash_message'] = "Le post a été supprimé avec succès !";
				$_SESSION['flash_type'] = "success";
				header('Location: /Blog/admin/showPost');
				exit;
			} catch (PDOException $e) {
				$_SESSION['flash_message'] = "Une erreur s'est produite lors de la suppression du post : " . $e->getMessage();
				$_SESSION['flash_type'] = "danger";
				header('Location: /Blog/admin/showPost');
				exit;
			}
		}
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 * @throws \Exception
		 */
		public function updatePost($postId)
		{
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$title = $_POST['title'];
				$chapo = $_POST['chapo'];
				$author = $_POST['author'];
				$content = $_POST['content'];
				
				$postRepository = new PostRepository();
				$postRepository->updatePost($postId, $title, $chapo, $author, $content);
				$_SESSION['flash_message'] = "Le post a été mis à jour avec succès !";
				$_SESSION['flash_type'] = "success";
				header('Location: /Blog/admin/showPost');
				exit();
			} else {
				$post = $this->postRepository->getPostById($postId);
				$_SESSION['flash_message'] = "Le post a été mis à jour avec succès !";
				$_SESSION['flash_type'] = "success";
				$this->render('Admin/adminUpdatePost.html.twig', ['post' => $post]);
			}
		}
	}
	