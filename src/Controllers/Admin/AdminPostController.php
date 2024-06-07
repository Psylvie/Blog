<?php
	namespace App\Controllers\Admin;
	
	use App\Controllers\Controller;
	use App\Repository\PostRepository;
	use App\Utils\Superglobals;
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
			$requestMethod = Superglobals::getServer('REQUEST_METHOD');
			
			if ($requestMethod === 'POST') {
				$title = trim(htmlspecialchars(Superglobals::getPost('title') ?? ''));
				$chapo = trim(htmlspecialchars(Superglobals::getPost('chapo') ?? ''));
				$author = trim(htmlspecialchars(Superglobals::getPost('author') ?? ''));
				$content = trim(htmlspecialchars(Superglobals::getPost('content') ?? ''));
				$published = filter_var(Superglobals::getPost('published'), FILTER_VALIDATE_INT) ?? 0;
				$userId = Superglobals::getSession('user_id');
				$image = null;
				if (empty($title) || empty($chapo) || empty($author) || empty($content)) {
					Superglobals::setFlashMessage("danger", "Tous les champs sont requis.");
					header('Location: /Blog/admin/newPost');
					exit;
				}
				
				if (Superglobals::getFiles('image')['error'] === 0) {
					$allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
					$filename = Superglobals::getFiles('image')['name'];
					$filetype = Superglobals::getFiles('image')['type'];
					$filesize = Superglobals::getFiles('image')['size'];
					
					$extension = pathinfo($filename, PATHINFO_EXTENSION);
					if (!array_key_exists($extension, $allowed) || !in_array($filetype, $allowed)) {
						Superglobals::setFlashMessage("danger", "Erreur de type de fichier");
					}
					if ($filesize > 1024 * 1024) {
						Superglobals::setFlashMessage("danger", "Erreur de taille de fichier");
					}
					$newname = md5(uniqid());
					$newfilename = UPLOADS_POST_PATH . $newname . '.' . $extension;
					move_uploaded_file(Superglobals::getFiles('image')['tmp_name'], $newfilename);
					$image = $newname . '.' . $extension;
				}
				try {
					$postRepository = new PostRepository();
					$postRepository->createPost($title, $chapo, $author, $content, $image, $userId, $published);
					Superglobals::setFlashMessage("success", "Le post a été créé avec succès !");
					header('Location: /Blog/admin/showPost');
					exit;
				} catch (PDOException $e) {
					Superglobals::setFlashMessage("danger", "Une erreur s'est produite lors de la création du post : " . $e->getMessage());
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
				Superglobals::setFlashMessage("success", "Le post a été supprimé avec succès !");
				header('Location: /Blog/admin/showPost');
				exit;
			} catch (PDOException $e) {
				Superglobals::setFlashMessage("danger", "Une erreur s'est produite lors de la suppression du post : " . $e->getMessage());
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
			if (Superglobals::getServer('REQUEST_METHOD') === 'POST'){
				$title = trim(htmlspecialchars(Superglobals::getPost('title') ?? ''));
				$chapo = trim(htmlspecialchars(Superglobals::getPost('chapo') ?? ''));
				$author = trim(htmlspecialchars(Superglobals::getPost('author') ?? ''));
				$content = trim(htmlspecialchars(Superglobals::getPost('content') ?? ''));
				$published = filter_var(Superglobals::getPost('published'), FILTER_VALIDATE_INT) ?? 0;

				if (empty($title) || empty($chapo) || empty($author) || empty($content)) {
					throw new \Exception("Tous les champs sont requis.");
				}
				$post = $this->postRepository->getPostById($postId);
				$currentImage = $post->getImage();
				$image = $currentImage;
				
				if (Superglobals::getFiles('image')['error'] === 0) {
					$allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
					$filename = Superglobals::getFiles('image')['name'];
					$filetype = Superglobals::getFiles('image')['type'];
					$filesize = Superglobals::getFiles('image')['size'];
					$extension = pathinfo($filename, PATHINFO_EXTENSION);
					
					if (!array_key_exists($extension, $allowed) || !in_array($filetype, $allowed)) {
						Superglobals::setFlashMessage("danger", "Erreur de type de fichier");
					} elseif ($filesize > 1024 * 1024) {
						Superglobals::setFlashMessage("danger", "Erreur de taille de fichier");
					} else {
						$newname = md5(uniqid());
						$newfilename = UPLOADS_POST_PATH . $newname . '.' . $extension;
						if (move_uploaded_file(Superglobals::getFiles('image')['tmp_name'], $newfilename)) {
							$image = $newname . '.' . $extension;
							if ($currentImage && $currentImage !== 'defaultImage.jpg') {
								$currentImagePath = UPLOADS_POST_PATH . $currentImage;
								if (file_exists($currentImagePath)) {
									unlink($currentImagePath);
								}
							}
						} else {
							Superglobals::setFlashMessage("danger", "Erreur lors de l'envoi du fichier");
						}
					}
				}
				try {
					$this->postRepository->updatePost($postId, $title, $chapo, $author, $content, $image, $published);
					Superglobals::setFlashMessage("success", "Le post a été mis à jour avec succès !");
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
	