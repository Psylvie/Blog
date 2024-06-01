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
		 * @throws Exception
		 */
		public function registrationForm()
		{
			$csrfToken = bin2hex(random_bytes(32));
			$this->setSessionData('csrfToken', $csrfToken);
			$this->render('Auth/register.html.twig',[
				'csrfToken' => $csrfToken
			]);
		}
		
		/**
		 * @throws \Exception
		 */
		public function register()
		{
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$csrfToken = filter_input(INPUT_POST, 'csrfToken', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				if (!hash_equals($this->getSessionData('csrfToken'), $csrfToken)) {
					$this->setFlashMessage("danger", "Jeton CSRF invalide");
					header("Location: /Blog/inscription");
					exit();
				}
				$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$lastName = filter_input(INPUT_POST, "lastName", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$pseudo = filter_input(INPUT_POST, "pseudo", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
				$role = filter_input(INPUT_POST, "role", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$resetToken = filter_input(INPUT_POST, "resetToken", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				
				if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/', $password)) {
					$this->setFlashMessage("danger", "Le mot de passe doit contenir entre 8 et 15 caractères, dont au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.");
					header("Location: /Blog/inscription");
					exit();
				}
				$hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);
				$image = null;
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
					$this->setFlashMessage("success", "Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.");
					header("Location: /Blog/");
					exit();
				} catch (Exception $e) {
					$this->setFlashMessage("danger", "Erreur lors de la création de votre compte : " . $e->getMessage());
					header("Location: /Blog/inscription");
					exit();
				}
			}
		}
	}