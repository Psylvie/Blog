<?php
	
	namespace App\Controllers;
	require_once __DIR__ . '/../../vendor/autoload.php';
	require_once __DIR__ . '/../Config/MailConfig.php';
	require_once __DIR__ . '/../Config/Recaptcha.php';
	use App\Repository\PostRepository;
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
			session_start();
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
			$posts = $this->postRepository->findLatestPosts(3);
			$this->render('Home/homePage.html.twig', ['posts' => $posts]);
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
					$_SESSION['alert_message'] = "Tous les champs sont obligatoires !";
					header('Location: /Blog/');
					exit;
				}
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$_SESSION['alert_message'] = "L'adresse email n'est pas valide !";
					header('Location: /Blog/');
					exit;
				}
				$recaptchaResponse = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
				if (empty($recaptchaResponse)) {
					$_SESSION['alert_message'] = "Veuillez cocher la case reCAPTCHA !";
					header('Location: /Blog/');
					exit;
				}
				$recaptchaSecretKey = RECAPTCHA_SECRET_KEY;
				$recaptchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
				$recaptchaVerifyResponse = file_get_contents($recaptchaVerifyUrl . '?secret=' . $recaptchaSecretKey . '&response=' . $recaptchaResponse);
				$recaptchaData = json_decode($recaptchaVerifyResponse);
				
				if (!$recaptchaData->success) {
					$_SESSION['alert_message'] = "Erreur de validation reCAPTCHA !";
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
					$_SESSION['alert_message'] = "Le formulaire a été envoyé avec succès !";
					$_SESSION['alert_type'] = "success";
					echo '<script type="text/javascript">alert("Le formulaire a été envoyé avec succès !")</script>';
				} catch (Exception $e) {
					$_SESSION['alert_message'] = "Erreur d'envoi de mail !";
					$_SESSION['alert_type'] = "danger";
					echo '<script type="text/javascript">alert("Echec, veuillez reéssayer ultérieurement")</script>';
				}
				$this->homePage();
			}
		}
		
	}
	

	
