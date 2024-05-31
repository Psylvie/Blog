<?php
	
	namespace App\Controllers;
	
	
	use App\Repository\UserRepository;
	use Exception;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	include __DIR__ . '/../config/Config.php';
	
	class UserController extends Controller
	{
		
		private UserRepository $userRepository;
		
		public function __construct()
		{
			parent::__construct();
			$this->userRepository = new UserRepository();
		}
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 * @throws Exception
		 */
		public function show($userId)
		{
			session_start();
			if (!isset($_SESSION['user_id'])) {
				header("Location: /Blog/login");
				exit();
			}
			
			if ($userId != $_SESSION['user_id']) {
				header('Location: /Blog/user/' . $_SESSION['user_id']);
				exit();
			}
			
			$user = $this->userRepository->find($userId);
			$this->render('Users/user.html.twig', ['user' => $user]);
		}
		
		/**
		 * @throws Exception
		 */
		public function deleteUser($userId): void
		{
			if (!isset($_SESSION['user_id'])) {
				header("Location: /Blog/login");
				exit();
			}
			
			$user = $this->userRepository->find($userId);
			if ($user === null) {
				$_SESSION['flash_message'] = "L'utilisateur n'existe pas.";
				$_SESSION['flash_type'] = "danger";
				header("Location: /Blog/user/{$userId}");
				exit();
			}
			
			if (!password_verify($_POST['currentPassword'], $user->getPassword())) {
				$_SESSION['flash_message'] = "Le mot de passe actuel est incorrect.";
				$_SESSION['flash_type'] = "danger";
				header("Location: /Blog/user/{$userId}");
				exit();
			}
			
			$this->userRepository->delete($userId);
			if ($user->getImage() !== null) {
				unlink(UPLOADS_PROFILE_PATH . $user->getImage());
			}
			
			$_SESSION['flash_message'] = "Votre profil a été supprimé avec succès.";
			$_SESSION['flash_type'] = "success";
			session_destroy();
			header("Location: /Blog/");
			exit();
		}
		
		/**
		 * @throws RuntimeError
		 * @throws SyntaxError
		 * @throws LoaderError
		 * @throws Exception
		 */
		
		public function updateProfile(): void
		{
			if ($_SERVER["REQUEST_METHOD"] === "POST") {
				$userId = $_SESSION['user_id'] ?? null;
				$name = $_POST['name'] ?? '';
				$lastName = $_POST['lastName'] ?? '';
				$pseudo = $_POST['pseudo'] ?? '';
				$email = $_POST['email'] ?? '';
				$currentPassword = $_POST['currentPassword'] ?? '';
				$newPassword = $_POST['newPassword'] ?? '';
				$confirmPassword = $_POST['confirmPassword'] ?? '';
				
				if ($userId !== null) {
					$user = $this->userRepository->find($userId);
					
					if ($user !== null && password_verify($currentPassword, $user->getPassword())) {
						if ($newPassword === $confirmPassword) {
							$image = $user->getImage();
							if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
								$allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
								$filename = $_FILES['image']['name'];
								$filetype = $_FILES['image']['type'];
								$filesize = $_FILES['image']['size'];
								
								$extension = pathinfo($filename, PATHINFO_EXTENSION);
								if (!array_key_exists($extension, $allowed) || !in_array($filetype, $allowed)) {
									$_SESSION['flash_message'] = "Erreur de type de fichier";
									$_SESSION['flash_type'] = "danger";
								}
								if ($filesize > 1024 * 1024) {
									$_SESSION['flash_message'] = "Erreur de taille de fichier";
									$_SESSION['flash_type'] = "danger";
								}
								$newname = md5(uniqid());
								$newfilename = UPLOADS_PROFILE_PATH . $newname . '.' . $extension;
								if (move_uploaded_file($_FILES['image']['tmp_name'], $newfilename)) {
									if ($image !== 'avatar.png') {
										$oldImage = $image;
										if ($oldImage !== null) {
											unlink(UPLOADS_PROFILE_PATH . $oldImage);
										}
									}
									$image = $newname . '.' . $extension;
								} else {
									$_SESSION['flash_message'] = "Erreur lors du téléchargement de l'image.";
									$_SESSION['flash_type'] = "danger";
								}
							}
							$this->userRepository->updateProfile($userId, $name, $image, $lastName, $email, $pseudo);
							if (!empty($newPassword)) {
								$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
								$this->userRepository->updatePassword($email, $hashedPassword);
							}
							$_SESSION['flash_message'] = "Les informations du profil ont été mises à jour avec succès.";
							$_SESSION['flash_type'] = "success";
							header("Location: /Blog/user/{$userId}");
							exit();
						} else {
							$_SESSION['flash_message'] = "Le nouveau mot de passe et la confirmation ne correspondent pas.";
						}
					} else {
						$_SESSION['flash_message'] = "Les données ne sont pas correctes ou le mot de passe actuel est incorrect.";
						$_SESSION['flash_type'] = "danger";
						header("Location: /Blog/user/{$userId}");
					}
				}
			}
		}
		
	}
	