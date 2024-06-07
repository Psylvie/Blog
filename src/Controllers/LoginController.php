<?php
	
	namespace App\Controllers;
	require_once __DIR__ . '/../config/MailConfig.php';
	use App\Repository\UserRepository;
	use App\Utils\Superglobals;
	use Exception;
	use JetBrains\PhpStorm\NoReturn;
	use PHPMailer\PHPMailer\PHPMailer;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	
	class LoginController extends Controller
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
		public function loginForm()
		{
			$this->render('Auth/login.html.twig');
		}
		
		/**
		 * @throws Exception
		 */
		public function login()
		{
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$email = Superglobals::getPost('email');
				$password = Superglobals::getPost('password');
				$userRepository = new UserRepository();
				$user = $userRepository->findByEmail($email);
				if ($user) {
					if (!password_verify($password, $user->getPassword())) {
						Superglobals::setFlashMessage("danger", "Mot de passe incorrect");
						
						header('Location: /MonBlog/login');
					} else {
						Superglobals::setSession('user_id', $user->getId());
						Superglobals::setSession('user_name', $user->getName());
						Superglobals::setSession('user_last_name', $user->getLastName());
						Superglobals::setSession('user_pseudo', $user->getPseudo());
						Superglobals::setSession('user_role', $user->getRole());
						Superglobals::setSession('user_email', $user->getEmail());
						Superglobals::setFlashMessage("success", "Bienvenue, " . $user->getName() . " ! Connexion réussie !");
						
						if (!$user->getFirstLoginDone()) {
							$userRepository->updateFirstLoginDone($user->getId(), true);
						}
						if ($user->getRole() == 'admin') {
							header('Location: /Blog/admin');
						} else {
							header('Location: /Blog/');
						}
						if (isset($_POST['remember_me']) && $_POST['remember_me'] == 1) {
							/** cookie end 30 days  */
							setcookie('user_id', $user->getId(), time() + (86400 * 30), "/");
						}
					}
				} else {
					Superglobals::setFlashMessage("danger", "Utilisateur non trouvé");
					header('Location: /Blog/login');
				}
				exit();
			}
		}
		
		
		/**
		 * @throws RuntimeError
		 * @throws SyntaxError
		 * @throws LoaderError
		 */
		public function showPasswordResetForm()
		{
			$this->render('Auth/passwordReset.html.twig');
		}
		
		/**
		 * @throws Exception
		 */
		public function requestPasswordReset()
		{
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$email = Superglobals::getPost('email');
				
				$userRepository = new UserRepository();
				$user = $userRepository->findByEmail($email);
				
				if ($user) {
					$resetToken = uniqid();
					$userRepository->setResetToken($user->getEmail(), $resetToken);
					
					$this->sendPasswordResetEmail($email, $resetToken);
					Superglobals::setFlashMessage("success", "Un e-mail de réinitialisation du mot de passe a été envoyé à votre adresse e-mail.");
				} else {
					Superglobals::setFlashMessage("danger", "Aucun utilisateur trouvé avec cette adresse e-mail.");
				}
				header('Location: /Blog/login');
				exit();
			}
		}
		
		private function sendPasswordResetEmail($email, $token)
		{
			$mail = new PHPMailer(true);
			
			try {
				$mail->isSMTP();
				$mail->Host = MAIL_SMTP_HOST;
				$mail->SMTPAuth = true;
				$mail->Username = MAIL_SMTP_USERNAME;
				$mail->Password = MAIL_SMTP_PASSWORD;
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->Port = MAIL_SMTP_PORT;
				
				$mail->setFrom(MAIL_SMTP_USERNAME, 'Blog');
				$mail->addAddress($email);
				
				$mail->isHTML(true);
				$mail->Subject = 'Reinitialisation du mot de passe';
				$mail->Body = "Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='http://localhost/Blog/newPassword/$token'>Réinitialiser le mot de passe</a>";
				
				$mail->send();
				Superglobals::setFlashMessage("success", "Le message a bien été envoyé");
			}catch (Exception $e) {
				Superglobals::setFlashMessage("danger", "Erreur d'envoi de mail : " . $mail->ErrorInfo);
			}
		}
		
		/**
		 * @throws RuntimeError
		 * @throws SyntaxError
		 * @throws LoaderError
		 * @throws Exception
		 */
		public function newPassword($token)
		{
			$userRepository = new UserRepository();
			$user = $userRepository->findByResetToken($token);
			
			if ($user) {
				$userByEmail = $userRepository->findByEmail($user->getEmail());
				
				if ($userByEmail) {
					$this->render('Auth/newPassword.html.twig', [
						'resetToken' => $token,
						'userId' => $userByEmail->getId(),
						'email' => $userByEmail->getEmail()
					]);
					return;
				} else {
					Superglobals::setFlashMessage("danger", "Utilisateur non trouvé");
					header('Location: /Blog/login');
					exit();
				}
			} else {
				Superglobals::setFlashMessage("danger", "Token de réinitialisation invalide.");
				header('Location: /Blog/login');
				exit();
			}
		}
		
		
		
		/**
		 * @throws Exception
		 */
		public function resetPassword()
		{
			if ($_SERVER["REQUEST_METHOD"] === "POST") {
				$resetToken = Superglobals::getPost('resetToken');
				$password = Superglobals::getPost('password');
				$confirmPassword = Superglobals::getPost('confirm_password');
				
				$pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
				if (!preg_match($pattern, $password)) {
					Superglobals::setFlashMessage("danger", "Le mot de passe doit contenir au moins 8 caractères, dont au moins une lettre majuscule, une lettre minuscule, et un chiffre.");
					header('Location: /Blog/newPassword/' . $resetToken);
					exit();
				}
				if ($password !== $confirmPassword) {
					Superglobals::setFlashMessage("danger", "Les mots de passe ne correspondent pas.");
					header('Location: /Blog/newPassword/' . $resetToken);
					exit();
					
				}
				
				$userRepository = new UserRepository();
				$user = $userRepository->findByResetToken($resetToken);
				if ($user) {
					$userRepository->updatePassword($user->getEmail(), password_hash($password, PASSWORD_DEFAULT));
					$userRepository->setResetToken($user->getEmail(), null);
					Superglobals::setFlashMessage("success", "Votre mot de passe a été réinitialisé avec succès.");
					header('Location: /Blog/');
					exit();
				}
			}
		}
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 */
		public function firstConnectionForm()
		{
			$this->render('Auth/firstConnection.html.twig');
		}
		
		/**
		 * @throws Exception
		 */
		public function handleFirstConnection()
		{
			
			if ($_SERVER["REQUEST_METHOD"] === "POST") {
				$email = Superglobals::getPost('email');
				
				$userRepository = new UserRepository();
				$user = $userRepository->findByEmail($email);
				
				if($user && $user->getFirstLoginDone() === false) {
					$resetToken = uniqid();
					$userRepository->setResetToken($user->getEmail(), $resetToken);
					
					$this->sendPasswordResetEmail($email, $resetToken);
					Superglobals::setFlashMessage("success", "Un e-mail de réinitialisation du mot de passe a été envoyé à votre adresse e-mail.");
					header('Location: /Blog/login');
				} else {
					Superglobals::setFlashMessage("danger", "Aucun utilisateur trouvé avec cette adresse e-mail.");
					header('Location: /Blog/first-connection');
				}
				exit();
				
			}
		}
		
		/**
		 * @return void
		 */
		#[NoReturn] public function logout(): void
		{
			Superglobals::unsetSession('user_id');
			session_destroy();
			session_start();
			Superglobals::setFlashMessage('success', 'Vous êtes déconnecté');
			$defaultCsrfToken = '';
			Superglobals::setSession('csrfToken', $defaultCsrfToken);
		if (isset($_COOKIE['user_id'])) {
			setcookie('user_id', '', time() - 3600, '/');
		}
			header('Location: /Blog/');
			exit();
		}

	}
