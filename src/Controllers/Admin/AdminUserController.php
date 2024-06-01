<?php
	
	namespace App\Controllers\Admin;
	
	use App\Controllers\Controller;
	use App\Repository\UserRepository;
	use Exception;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	include __DIR__ . '/../../config/Config.php';
	
	class AdminUserController extends Controller
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
		 * @throws \Exception
		 */
		public function list()
		{
			$users = $this->userRepository->findAll();
			$this->render('Admin/adminUsersList.html.twig', ['users' => $users]);
		}
		
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 * @throws \Exception
		 */
		public function show($userId)
		{
			$user = $this->userRepository->find($userId);
			$this->render('Admin/adminUpdateUserProfile.html.twig', ['user' => $user]);
		}
		
		public function delete($userId)
		{
			try {
				$this->userRepository->delete($userId);
				$user = $this->userRepository->find($userId);
				if ($user && $user->getImage()) {
					unlink(UPLOADS_PROFILE_PATH . $user->getImage());
				}
				$this->setFlashMessage('success', "L'utilisateur $userId a été supprimé avec succès.");
			} catch (Exception $e) {
				$this->setFlashMessage('danger', "Une erreur s'est produite lors de la suppression de l'utilisateur.");
			}
			header("Location: /Blog/admin/users/list");
			exit();
		}
		
		/**
		 * @throws Exception
		 */
		public function update($userId)
		{
			if ($_SERVER["REQUEST_METHOD"] === "POST") {
				$name = $_POST['name'] ?? '';
				$lastName = $_POST['lastName'] ?? '';
				$email = $_POST['email'] ?? '';
				$pseudo = $_POST['pseudo'] ?? '';
				$role = $_POST['role'] ?? '';
				$user = $this->userRepository->find($userId);
				if ($user === null) {
					$this->setFlashMessage("danger", "L'utilisateur n'existe pas.");
					header("Location: /Blog/admin/users/list");
					exit();
				}
				
				$image = $user->getImage(); // Conserver l'ancienne image si elle n'est pas modifiée
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
				}
				
				try {
					$this->userRepository->updateProfile($userId, $name, $image, $lastName, $email, $pseudo, $role);
					$this->setFlashMessage("success", "Les informations de l'utilisateur $name $lastName ont été mises à jour avec succès.");
				} catch (Exception $e) {
					$this->setFlashMessage("danger", "Une erreur s'est produite lors de la mise à jour des informations de l'utilisateur.");
				}
				header("Location: /Blog/admin/users/list");
				exit();
			}
		}
		
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 */
		public function createUser()
		{
			
			$this->render('Admin/adminCreateUser.html.twig');
		}
		
		public function createUserProcess()
		{
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$name = isset($_POST["name"]) ? trim($_POST["name"]) : '';
				$lastName = isset($_POST["lastName"]) ? trim($_POST["lastName"]) : '';
				$pseudo = isset($_POST["pseudo"]) ? trim($_POST["pseudo"]) : '';
				$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
				$role = isset($_POST["role"]) ? trim($_POST["role"]) : '';
				$resetToken = isset($_POST["resetToken"]) ? trim($_POST["resetToken"]) : '';
				$hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);
				$image = null;
				$firstLoginDone = false;
				
				if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
					$allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
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
					$newfilename = UPLOADS_PROFILE_PATH . $newname . '.' . $extension;
					move_uploaded_file($_FILES['image']['tmp_name'], $newfilename);
					$image = $newname . '.' . $extension;
				}
				try {
					$userRepository = new UserRepository();
					$userRepository->createUser($name, $lastName, $image, $pseudo, $email, $hashedPassword, $role, $resetToken);
					$this->setFlashMessage("success", "L'utilisateur a été créé avec succès !");
					header("Location: /Blog/admin/users/list");
					exit();
				} catch (Exception $e) {
					$this->setFlashMessage("danger", "Une erreur s'est produite lors de la création de l'utilisateur : " . $e->getMessage());
					header("Location: /Blog/admin/users/create");
					exit();
				}
			}
		}
	}
	