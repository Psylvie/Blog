<?php

namespace App\Controllers;
require_once __DIR__ . '/../config/MailConfig.php';

use App\Utils\CsrfProtection;
use App\Utils\Superglobals;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class LoginController
 * @package App\Controllers
 */
class LoginController extends Controller
{
    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * login form
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function loginForm()
    {
        $csrfToken = CsrfProtection::generateToken();
        Superglobals::setSession('csrfToken', $csrfToken);
        $this->render('Auth/login.html.twig', [
            'csrfToken' => $csrfToken
        ]);
    }

    /**
     * login action
     * @throws Exception
     */
    public function login()
    {
        if (Superglobals::getServer('REQUEST_METHOD') === 'POST') {
            $csrfToken = Superglobals::getPost('csrfToken');
            if (!CsrfProtection::checkToken($csrfToken)) {
                Superglobals::setFlashMessage("danger", "Token CSRF invalide");
                $this->redirect('/Blog/login');
            }
            $email = Superglobals::getPost('email');
            $password = Superglobals::getPost('password');
            $user = $this->userRepository->findByEmail($email);
            if ($user) {
                if (!password_verify($password, $user->getPassword())) {
                    Superglobals::setFlashMessage("danger", "Mot de passe incorrect");
                    $this->redirect('/Blog/login');
                } else {
                    Superglobals::setSession('user_id', $user->getId());
                    Superglobals::setSession('user_name', $user->getName());
                    Superglobals::setSession('user_last_name', $user->getLastName());
                    Superglobals::setSession('user_pseudo', $user->getPseudo());
                    Superglobals::setSession('user_role', $user->getRole());
                    Superglobals::setSession('user_email', $user->getEmail());
                    Superglobals::setFlashMessage("success", "Bienvenue, " . $user->getName() . " ! Connexion réussie !");

                    if (!$user->getFirstLoginDone()) {
                        $this->userRepository->updateFirstLoginDone($user->getId(), true);
                    }
                    if ($user->getRole() == 'admin') {
                        $this->redirect('/Blog/admin');
                    } else {
                        $this->redirect('/Blog/');
                    }
                    $rememberMe = Superglobals::getPost('remember_me');
                    if (isset($rememberMe) && $rememberMe == 1) {
                        $cookieLifetime = time() + (86400 * 30); // 30 days
                        setcookie('user_id', $user->getId(), $cookieLifetime, "/");
                    }
                }
            } else {
                Superglobals::setFlashMessage("danger", "Utilisateur non trouvé");
                $this->redirect('/Blog/login');
            }
        }
    }


    /**
     * password reset form
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function showPasswordResetForm()
    {
        $this->render('Auth/passwordReset.html.twig');
    }

    /**
     * request password reset
     * @throws Exception
     */
    public function requestPasswordReset()
    {
        if (Superglobals::getServer('REQUEST_METHOD') === 'POST') {
            $csrfToken = Superglobals::getPost('csrfToken');
            if (!CsrfProtection::checkToken($csrfToken)) {
                Superglobals::setFlashMessage("danger", "Token CSRF invalide");
                $this->redirect('/Blog/login');
            }
            $email = Superglobals::getPost('email');

            $user = $this->userRepository->findByEmail($email);

            if ($user) {
                $resetToken = uniqid();
                $this->userRepository->setResetToken($user->getEmail(), $resetToken);

                $this->sendPasswordResetEmail($email, $resetToken);
                Superglobals::setFlashMessage("success", "Un e-mail de réinitialisation du mot de passe a été envoyé à votre adresse e-mail.");
            } else {
                Superglobals::setFlashMessage("danger", "Aucun utilisateur trouvé avec cette adresse e-mail.");
            }
            $this->redirect('/Blog/login');
        }
    }

    /**
     * send password reset email
     * @param $email
     * @param $token
     */
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
        } catch (Exception $e) {
            Superglobals::setFlashMessage("danger", "Erreur d'envoi de mail : " . $mail->ErrorInfo);
        }
    }

    /**
     * new password form
     * @param $token
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function newPassword($token)
    {
        $user = $this->userRepository->findByResetToken($token);

        if ($user) {
            $userByEmail = $this->userRepository->findByEmail($user->getEmail());

            if ($userByEmail) {
                $this->render('Auth/newPassword.html.twig', [
                    'resetToken' => $token,
                    'userId' => $userByEmail->getId(),
                    'email' => $userByEmail->getEmail()
                ]);
                return;
            } else {
                Superglobals::setFlashMessage("danger", "Utilisateur non trouvé");
                $this->redirect('/Blog/login');
            }
        } else {
            Superglobals::setFlashMessage("danger", "Token de réinitialisation invalide ou expiré.");
            $this->redirect('/Blog/login');
        }
    }


    /**
     * reset password
     * @throws Exception
     */
    public function resetPassword()
    {
        if (Superglobals::getServer('REQUEST_METHOD') === 'POST' ) {
            $resetToken = Superglobals::getPost('resetToken');
            $password = Superglobals::getPost('password');
            $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
            if (!preg_match($passwordPattern, $password)) {
                Superglobals::setFlashMessage("danger", "Le mot de passe doit contenir au moins 8 caractères, dont au moins une lettre majuscule, une lettre minuscule, et un chiffre.");
                $this->redirect('/Blog/newPassword/' . $resetToken);
            }
            $user = $this->userRepository->findByResetToken($resetToken);
            if ($user) {
                $this->userRepository->updatePassword($user->getEmail(), password_hash($password, PASSWORD_DEFAULT));
                $this->userRepository->setResetToken($user->getEmail(), null);
                Superglobals::setFlashMessage("success", "Votre mot de passe a été réinitialisé avec succès.");
                $this->redirect('/Blog/login');
            }
        }
    }

    /**
     * first connection form with not password
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function firstConnectionForm()
    {
        $this->render('Auth/firstConnection.html.twig');
    }

    /**
     * handle first connection with not password
     * @throws Exception
     */
    public function handleFirstConnection()
    {

        if (Superglobals::getServer('REQUEST_METHOD') === 'POST') {
            $csrfToken = Superglobals::getPost('csrfToken');
            if (!CsrfProtection::checkToken($csrfToken)) {
                Superglobals::setFlashMessage("danger", "Token CSRF invalide");
                $this->redirect('/Blog/first-connection');
            }
            $email = $this->testInput(Superglobals::getPost('email'));
            $user = $this->userRepository->findByEmail($email);

            if ($user && $user->getFirstLoginDone() === false) {
                $resetToken = uniqid();
                $this->userRepository->setResetToken($user->getEmail(), $resetToken);

                $this->sendPasswordResetEmail($email, $resetToken);
                Superglobals::setFlashMessage("success", "Un e-mail de réinitialisation du mot de passe a été envoyé à votre adresse e-mail.");
                $this->redirect('/Blog/login');
            } else {
                Superglobals::setFlashMessage("danger", "Ce compte a deja été initialisé ou n'existe pas.");
                $this->redirect('/Blog/first-connection');
            }
        }
    }

    /**
     * logout action
     * @return void
     */
    #[NoReturn] public function logout(): void
    {
        Superglobals::unsetSession('user_id');
        session_destroy();
        session_start();
        Superglobals::setFlashMessage('success', 'Vous êtes déconnecté');

       CsrfProtection::unsetToken();

        if (Superglobals::getCookie('user_id')) {
            setcookie('user_id', '', time() - 3600, '/');
        }
        $this->redirect('/Blog/login');
    }

}
