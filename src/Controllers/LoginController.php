<?php
	
	namespace App\Controllers;
	require_once __DIR__ . '/../config/MailConfig.php';
	use App\Repository\UserRepository;
	use Exception;
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
				$email = $_POST['email'];
				$password = $_POST['password'];
				$userRepository = new UserRepository();
				$user = $userRepository->findByEmail($email);
				if ($user) {
					if (!password_verify($password, $user->getPassword())) {
						$_SESSION['flash_message'] = "Mot de passe incorrect";
						$_SESSION['flash_type'] = "danger";
						header('Location: /MonBlog/login');
					} else {
						$_SESSION['user_id'] = $user->getId();
						$_SESSION['user_name'] = $user->getName();
						$_SESSION['user_last_name'] = $user->getLastName();
						$_SESSION['user_pseudo'] = $user->getPseudo();
						$_SESSION['user_role'] = $user->getRole();
						$_SESSION['user_email'] = $user->getEmail();
						$_SESSION['flash_message'] = "Bienvenue, " . $user->getName() . " ! Connexion réussie !";
						$_SESSION['flash_type'] = "success";
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
					$_SESSION['flash_message'] = "Utilisateur non trouvé";
					$_SESSION['flash_type'] = "danger";
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
				$email = $_POST['email'];
				
				$userRepository = new UserRepository();
				$user = $userRepository->findByEmail($email);
				
				if ($user) {
					$resetToken = uniqid();
					$userRepository->setResetToken($user->getEmail(), $resetToken);
					
					$this->sendPasswordResetEmail($email, $resetToken);
					
					$_SESSION['flash_message'] = "Un e-mail de réinitialisation du mot de passe a été envoyé à votre adresse e-mail.";
					$_SESSION['flash_type'] = "success";
				} else {
					
					$_SESSION['flash_message'] = "Aucun utilisateur trouvé avec cette adresse e-mail.";
					$_SESSION['flash_type'] = "danger";
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
				echo "Le message a bien été envoyé";
			}catch (Exception $e) {
				echo "Erreur d'envoi de mail : " . $mail->ErrorInfo;
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
					$_SESSION['flash_message'] = "Utilisateur non trouvé.";
					$_SESSION['flash_type'] = "danger";
					header('Location: /Blog/login');
					exit();
				}
			} else {
				$_SESSION['flash_message'] = "Token de réinitialisation invalide.";
				$_SESSION['flash_type'] = "danger";
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
				$resetToken = $_POST['resetToken'];
				$password = $_POST['password'];
				$confirmPassword = $_POST['confirm_password'];
				if ($password !== $confirmPassword) {
					$_SESSION['flash_message'] = "Les mots de passe ne correspondent pas.";
					$_SESSION['flash_type'] = "danger";
					header('Location: /Blog/newPassword/' . $resetToken);
					exit();
					
				}
				
				$userRepository = new UserRepository();
				$user = $userRepository->findByResetToken($resetToken);
				if ($user) {
					$userRepository->updatePassword($user->getEmail(), password_hash($password, PASSWORD_DEFAULT));
					$userRepository->setResetToken($user->getEmail(), null);
					$_SESSION['flash_message'] = "Votre mot de passe a été reinitialisé avec succès.";
					$_SESSION['flash_type'] = "success";
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
				$email = $_POST['email'];
				
				$userRepository = new UserRepository();
				$user = $userRepository->findByEmail($email);
				
				if($user && $user->getFirstLoginDone() === false) {
					$resetToken = uniqid();
					$userRepository->setResetToken($user->getEmail(), $resetToken);
					
					$this->sendPasswordResetEmail($email, $resetToken);
					
					$_SESSION['flash_message'] = "Un e-mail de réinitialisation du mot de passe a été envoyé à votre adresse e-mail.";
					$_SESSION['flash_type'] = "success";
					
					header('Location: /Blog/login');
				} else {
					$_SESSION['flash_message'] = "Aucun utilisateur trouvé avec cette adresse e-mail.";
					$_SESSION['flash_type'] = "danger";
					header('Location: /Blog/first-connection');
				}
				exit();
				
			}
		}
		
		
		
		/**
		 * @return void
		 */
		public function logout(): void
		{
						$_SESSION['flash_message'] = "Vous êtes déconnecté";
			$_SESSION['flash_type'] = "success";
			session_unset();
			session_destroy();
//		if (isset($_COOKIE['user_id'])) {
//			setcookie('user_id', '', time() - 3600, '/');
//		}

			header('Location: /Blog/');
			exit();
		}

	}
