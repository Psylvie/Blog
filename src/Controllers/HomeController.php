<?php

namespace App\Controllers;
require_once __DIR__ . '/../Config/MailConfig.php';
require_once __DIR__ . '/../Config/Recaptcha.php';

use App\Utils\CsrfProtection;
use App\Utils\Superglobals;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class HomeController
 * @package App\Controllers
 */
class HomeController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Display the home page
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws \Exception
     */
    public function homePage()
    {
        $userId = Superglobals::getSession('user_id', FILTER_VALIDATE_INT);
        $user = $userId ? $this->userRepository->find($userId) : null;
        $posts = $this->postRepository->findLatestPosts(3);
        $this->render('Home/homePage.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'recaptchaSiteKey' => RECAPTCHA_SITE_KEY]);
    }

    /**
     * Handle the contact form
     * @throws SyntaxError
     * @throws \Exception
     */
    public function contactForm()
    {
        if (Superglobals::getServer('REQUEST_METHOD') === 'POST') {
            $csrfToken = Superglobals::getPost('csrfToken') ?? '';
            if (!CsrfProtection::checkToken($csrfToken)) {
                Superglobals::setFlashMessage("danger", "Erreur de validation CSRF !");
                $this->redirect('/Blog/');
            }

            $firstName = $this->testInput(Superglobals::getPost('firstName') ?? '');
            $lastName = $this->testInput(Superglobals::getPost('lastName') ?? '');
            $email = $this->testInput(Superglobals::getPost('email') ?? '');
            $message = $this->testInput(Superglobals::getPost('message') ?? '');

            if (empty($firstName) || empty($lastName) || empty($email) || empty($message)) {
                Superglobals::setFlashMessage("info", "Tous les champs sont obligatoires !");
                $this->redirect('/Blog/');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Superglobals::setFlashMessage("info", "L'adresse email n'est pas valide !");
                $this->redirect('/Blog/');
            }
            $recaptchaResponse = Superglobals::getPost('g-recaptcha-response') ?? '';
            if (empty($recaptchaResponse)) {
                Superglobals::setFlashMessage("info", "Veuillez cocher la case reCAPTCHA !");
                $this->redirect('/Blog/');
            }
            $recaptchaSecretKey = RECAPTCHA_SECRET_KEY;
            $recaptchaVerifyUrl = RECAPTCHA_URL;
            $recaptchaVerifyResponse = file_get_contents($recaptchaVerifyUrl . '?secret=' . $recaptchaSecretKey . '&response=' . $recaptchaResponse);
            $recaptchaData = json_decode($recaptchaVerifyResponse);

            if (!$recaptchaData->success) {
                Superglobals::setFlashMessage("danger", "Erreur de validation reCAPTCHA !");
                $this->redirect('/Blog/');
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
                Superglobals::setFlashMessage("success", "Votre demande a été envoyé !");
            } catch (Exception $e) {
                Superglobals::setFlashMessage("danger", "Erreur d'envoi de mail !");
            }
            $this->homePage();
        }
    }

    /**
     * Display the contact page
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws \Exception
     */
    public function contact()
    {
        $csrfToken = CsrfProtection::generateToken();
        Superglobals::setSession('csrfToken', $csrfToken);
        $this->render('Home/contact.html.twig',
            ['recaptchaSiteKey' => RECAPTCHA_SITE_KEY,
                'csrfToken' => $csrfToken]);
    }

    /**
     * Display the privacy policy page
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function privacyPolicy()
    {
        $this->render('Home/privacyPolicy.html.twig');
    }

    /**
     * Display the legal mention page
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function legalMention()
    {
        $this->render('Home/legalMention.html.twig');
    }
}
