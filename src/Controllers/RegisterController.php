<?php
	
	namespace App\Controllers;
	
	use App\Repository\UserRepository;
	use Exception;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	include __DIR__ . '/../Config/Config.php';
	
	class RegisterController extends Controller
	{
		
		public function __construct()
		{
			parent::__construct();
		}
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 */
		public function registrationForm()
		{
			$this->render('Auth/register.html.twig');
		}
		
		/**
		 * @throws \Exception
		 */
		public function register()
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
					move_uploaded_file($_FILES['image']['tmp_name'], $newfilename);
					$image = $newname . '.' . $extension;
				}
				
				try {
					$userRepository = new UserRepository();
					$userRepository->createUser($name, $lastName, $image, $pseudo, $email, $hashedPassword, $role, $resetToken);
					$_SESSION['flash_message'] = "Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.";
					$_SESSION['flash_type'] = "success";
					header("Location: /Blog/");
					exit();
				} catch (Exception $e) {
					$_SESSION['flash_message'] = "Erreur lors de la création de votre compte : " . $e->getMessage();
					$_SESSION['flash_type'] = "danger";
					header("Location: /Blog/inscription");
					exit();
				}
			}
		}
	}