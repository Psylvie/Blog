<?php
	
	namespace App\Controllers;
	require_once __DIR__ . '/../Config/MailConfig.php';
	require_once __DIR__ . '/../Config/Recaptcha.php';
	use App\Repository\PostRepository;
	use App\Repository\UserRepository;
	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\PHPMailer;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	
	class HomeController extends Controller
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
		public function homePage()
		{
			$userRepository = new UserRepository();
			$userId = $this->getSessionData('user_id', FILTER_VALIDATE_INT);
			$user = $userId ? $userRepository->find($userId) : null;
			$posts = $this->postRepository->findLatestPosts(3);
			$this->render('Home/homePage.html.twig', ['user' => $user, 'posts' => $posts]);
		}
		
		/**
		 * @throws \Exception
		 */
		public function contactForm()
		{
			
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				
				$firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
				$lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
				$email = isset($_POST['email']) ? trim($_POST['email']) : '';
				$message = isset($_POST['message']) ? trim($_POST['message']) : '';
				
				if (empty($firstName) || empty($lastName) || empty($email) || empty($message)) {
					$this->setFlashMessage("info", "Tous les champs sont obligatoires !");
					header('Location: /Blog/');
					exit;
				}
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$this->setFlashMessage("info", "L'adresse email n'est pas valide !");
					header('Location: /Blog/');
					exit;
				}
				$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
				if (empty($recaptchaResponse)) {
					$this->setFlashMessage("info", "Veuillez cocher la case reCAPTCHA !");
					header('Location: /Blog/');
					exit;
				}
				$recaptchaSecretKey = RECAPTCHA_SECRET_KEY;
				$recaptchaVerifyUrl = RECAPTCHA_URL;
				$recaptchaVerifyResponse = file_get_contents($recaptchaVerifyUrl . '?secret=' . $recaptchaSecretKey . '&response=' . $recaptchaResponse);
				$recaptchaData = json_decode($recaptchaVerifyResponse);
				
				if (!$recaptchaData->success) {
					$this->setFlashMessage("danger", "Erreur de validation reCAPTCHA !");
					header('Location: /Blog/');
					
					exit;
				}
				
				$htmlMessage = "
                <html lang='fr'>
                <body>
                <h2>Nouveau message de $firstName $lastName</h2>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Message:</strong><br>$message</p>
                </body>
                </html>
            ";
				$mail = new PHPMailer(true);
				try {
					$mail->isSMTP();
					$mail->Host = MAIL_SMTP_HOST;
					$mail->SMTPAuth = true;
					$mail->Username = MAIL_SMTP_USERNAME;
					$mail->Password = MAIL_SMTP_PASSWORD;
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
					$mail->Port = MAIL_SMTP_PORT;
					
					$mail->setFrom($email, $firstName . ' ' . $lastName);
					$mail->addAddress(MAIL_SENDER_EMAIL);
					
					$mail->isHTML(true);
					$mail->Subject = 'Nouveau message de ' . $firstName . ' ' . $lastName;
					$mail->Body = $htmlMessage;
					
					$mail->send();
					$this->setFlashMessage("success", "Le formulaire a été envoyé avec succès !");
				} catch (Exception $e) {
					$this->setFlashMessage("danger", "Erreur d'envoi de mail !");

				}
				$this->homePage();
			}
		}
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 */
		public function contact()
		{
			$this->render('Home/contact.html.twig');
		}
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 */
		public function privacyPolicy()
		{
			$this->render('Home/privacyPolicy.html.twig');
		}
		
		/**
		 * @throws SyntaxError
		 * @throws RuntimeError
		 * @throws LoaderError
		 */
		public function legalMention()
		{
			$this->render('Home/legalMention.html.twig');
		}
	}
