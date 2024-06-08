<?php

namespace App\Controllers;

use App\Repository\UserRepository;
use App\Services\ImageService;
use App\Utils\Superglobals;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

include __DIR__ . '/../Config/Config.php';

/**
 * Class RegisterController
 * @package App\Controllers
 */
class RegisterController extends Controller
{
     protected ImageService $imageService;

    /**
     * RegisterController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->imageService = new ImageService();
    }

    /**
     * Registration form
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function registrationForm()
    {
        $csrfToken = bin2hex(random_bytes(32));
        Superglobals::setSession('csrfToken', $csrfToken);
        $this->render('Auth/register.html.twig', [
            'csrfToken' => $csrfToken
        ]);
    }

    /**
     * Registration action
     * @throws Exception
     */
    public function register()
    {
        if (Superglobals::getServer('REQUEST_METHOD') === 'POST') {
            $csrfToken = Superglobals::getPost('csrfToken');
            if (!hash_equals(Superglobals::getSession('csrfToken'), $csrfToken)) {
                Superglobals::setFlashMessage("danger", "Jeton CSRF invalide");
                $this->redirect('/Blog/inscription');
            }

            $name = Superglobals::getPost("name");
            $lastName = Superglobals::getPost("lastName");
            $pseudo = Superglobals::getPost("pseudo");
            $email = Superglobals::getPost("email");
            $role = Superglobals::getPost("role");
            $resetToken = Superglobals::getPost("resetToken");
            $password = Superglobals::getPost("password");

            $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
            if (!preg_match($pattern, $password)) {
                Superglobals::setFlashMessage("danger", "Le mot de passe doit contenir au moins 8 caractères, dont au moins une lettre majuscule, une lettre minuscule, et un chiffre.");
                $this->redirect('/Blog/inscription');
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $image = null;
            if (Superglobals::getFiles('image') && Superglobals::getFiles('image')['error'] === 0) {
                $image = $this->imageService->uploadImage(Superglobals::getFiles('image'), UPLOADS_PROFILE_PATH);
                if ($image === null) {
                    Superglobals::setFlashMessage("danger", "Erreur lors de l'envoi du fichier");
                    $this->redirect('/Blog/inscription');
                }
            }
            try {
                $userRepository = new UserRepository();
                $userRepository->createUser($name, $lastName, $image, $pseudo, $email, $hashedPassword, $role, $resetToken);
                Superglobals::setFlashMessage("success", "Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.");
                $this->redirect('/Blog/login');
            } catch (Exception $e) {
                Superglobals::setFlashMessage("danger", "Erreur lors de la création de votre compte : " . $e->getMessage());
                $this->redirect('/Blog/inscription');
            }
        }
    }
}
	