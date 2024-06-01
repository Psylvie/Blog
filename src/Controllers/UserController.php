<?php
	
	namespace App\Controllers;
	
	
	use App\Repository\UserRepository;
	use Exception;
	use JetBrains\PhpStorm\NoReturn;
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
			$user = $this->userRepository->find($userId);
			$this->render('Users/user.html.twig', ['user' => $user]);
		}
		
		/**
		 * @throws Exception
		 */
		#[NoReturn] public function deleteUser($userId): void
		{
			$userId = $this->getSessionData('user_id', FILTER_VALIDATE_INT);
			
			if (!$userId) {
				header("Location: /Blog/login");
				exit();
			}
			
			$user = $this->userRepository->find($userId);
			if ($user === null) {
				$this->setFlashMessage("danger", "L'utilisateur n'existe pas.");
				header("Location: /Blog/user/{$userId}");
				exit();
			}
			
			if (!password_verify($_POST['currentPassword'], $user->getPassword())) {
				$this->setFlashMessage("danger", "Le mot de passe actuel est incorrect.");
				header("Location: /Blog/user/{$userId}");
				exit();
			}
			
			$this->userRepository->delete($userId);
			if ($user->getImage() !== null) {
				unlink(UPLOADS_PROFILE_PATH . $user->getImage());
			}
			$this->setFlashMessage("success", "Votre compte a été supprimé avec succès.");
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
				$userId = $this->getSessionData('user_id', FILTER_VALIDATE_INT);				$name = $_POST['name'] ?? '';
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
									$this->setFlashMessage("danger", "Le type de fichier n'est pas autorisé.");
								}
								if ($filesize > 1024 * 1024) {
									$this->setFlashMessage("danger", "Le fichier est trop volumineux.");
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
									$this->setFlashMessage("danger", "Une erreur est survenue lors de l'envoi du fichier.");
								}
							}
							$this->userRepository->updateProfile($userId, $name, $image, $lastName, $email, $pseudo);
							if (!empty($newPassword)) {
								$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
								$this->userRepository->updatePassword($email, $hashedPassword);
							}
							$this->setSessionData('user_name', $name);
							$this->setSessionData('user_last_name', $lastName);
							$this->setSessionData('user_email', $email);
							$this->setSessionData('user_pseudo', $pseudo);
							$this->setFlashMessage("success", "Vos informations ont été mises à jour avec succès.");
							header("Location: /Blog/user/{$userId}");
							exit();
						} else {
							$this->setFlashMessage("danger", "Les mots de passe ne correspondent pas.");
						}
					} else {
						$this->setFlashMessage("danger", "Le mot de passe actuel est incorrect.");
						header("Location: /Blog/user/{$userId}");
					}
				}
			}
		}
		
	}
	