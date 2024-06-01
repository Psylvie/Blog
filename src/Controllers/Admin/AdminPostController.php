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
			$requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			
			if ($requestMethod === 'POST') {
				$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$title = trim($title);
				$chapo = filter_input(INPUT_POST, 'chapo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$chapo = trim($chapo);
				$author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$author = trim($author);
				$content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$content = trim($content);
				$published = filter_input(INPUT_POST, 'published', FILTER_VALIDATE_INT);
				$published = $published ?? 1;
				$userId = $this->getSessionData('user_id', FILTER_VALIDATE_INT);
				$image = null;
				if (empty($title) || empty($chapo) || empty($author) || empty($content)) {
					$this->setFlashMessage("danger", "All fields are required.");
					header('Location: /Blog/admin/newPost');
					exit;
				}
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
						$this->setFlashMessage("danger", "Erreur de type de fichier");
					}
					if ($filesize > 1024 * 1024) {
						$this->setFlashMessage("danger", "Erreur de taille de fichier");
					}
					$newname = md5(uniqid());
					$newfilename = UPLOADS_POST_PATH . $newname . '.' . $extension;
					move_uploaded_file($_FILES['image']['tmp_name'], $newfilename);
					$image = $newname . '.' . $extension;
				}
				try {
					$postRepository = new PostRepository();
					$postRepository->createPost($title, $chapo, $author, $content, $image, $userId, $published);
					$this->setFlashMessage("success", "Le post a été créé avec succès !");
					header('Location: /Blog/admin/showPost');
					exit;
				} catch (PDOException $e) {
					$this->setFlashMessage("danger", "Une erreur s'est produite lors de la création du post : " . $e->getMessage());
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
				$this->setFlashMessage("success", "Le post a été supprimé avec succès !");
				header('Location: /Blog/admin/showPost');
				exit;
			} catch (PDOException $e) {
				$this->setFlashMessage("danger", "Une erreur s'est produite lors de la suppression du post : " . $e->getMessage());
				header('Location: /Blog/admin/showPost');
				exit;
			}
		}
		
		/**
		 *
		 * @param int $postId
		 * @throws \Exception
		 */
		public function updatePost(int $postId)
		{
			if ($_SERVER["REQUEST_METHOD"] === "POST") {
				$title = isset($_POST['title']) ? trim(filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : '';
				$chapo = isset($_POST['chapo']) ? trim(filter_var($_POST['chapo'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : '';
				$author = isset($_POST['author']) ? trim(filter_var($_POST['author'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : '';
				$content = isset($_POST['content']) ? trim(filter_var($_POST['content'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : '';
				$published = isset($_POST['published']) ? filter_var($_POST['published'], FILTER_VALIDATE_INT) : 0;
				
				if (empty($title) || empty($chapo) || empty($author) || empty($content)) {
					throw new \Exception("Tous les champs sont requis.");
				}
				$post = $this->postRepository->getPostById($postId);
				$currentImage = $post->getImage();
				$image = $currentImage;
				
				if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
					$allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
					$filename = $_FILES['image']['name'];
					$filetype = $_FILES['image']['type'];
					$filesize = $_FILES['image']['size'];
					$extension = pathinfo($filename, PATHINFO_EXTENSION);
					
					if (!array_key_exists($extension, $allowed) || !in_array($filetype, $allowed)) {
						$this->setFlashMessage("danger", "Erreur de type de fichier");
					} elseif ($filesize > 1024 * 1024) {
						$this->setFlashMessage("danger", "Erreur de taille de fichier");
					} else {
						$newname = md5(uniqid());
						$newfilename = UPLOADS_POST_PATH . $newname . '.' . $extension;
						if (move_uploaded_file($_FILES['image']['tmp_name'], $newfilename)) {
							$image = $newname . '.' . $extension;
							if ($currentImage && $currentImage !== 'defaultImage.jpg') {
								$currentImagePath = UPLOADS_POST_PATH . $currentImage;
								if (file_exists($currentImagePath)) {
									unlink($currentImagePath);
								}
							}
						} else {
							$this->setFlashMessage("danger", "Erreur lors de l'envoi du fichier");
						}
					}
				}
				
				try {
					$this->postRepository->updatePost($postId, $title, $chapo, $author, $content, $image,  $published);
				} catch (\PDOException $e) {
					throw new \Exception("Erreur de connexion à la base de données : " . $e->getMessage());
				}
				
				header('Location: /Blog/admin/showPost');
				exit();
			} else {
				$post = $this->postRepository->getPostById($postId);
				$this->render('Admin/adminUpdatePost.html.twig', ['post' => $post]);
			}
		}
		
	}
	