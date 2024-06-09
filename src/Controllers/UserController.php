<?php

namespace App\Controllers;

use App\Repository\UserRepository;
use App\Services\ImageService;
use App\Utils\Superglobals;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

include __DIR__ . '/../Config/Config.php';

/**
 * Class UserController
 * @package App\Controllers
 */
class UserController extends Controller
{
    protected ImageService $imageService;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->imageService = new ImageService();
    }

    /**
     * Display the user profile
     * @throws Exception
     */
    public function show($userId)
    {
        $user = $this->userRepository->find($userId);
        $this->render('Users/user.html.twig', ['user' => $user]);
    }

    /**
     * Delete a user account
     * @throws Exception
     */
    #[NoReturn] public function deleteUser($userId): void
    {
        $userId = filter_var(Superglobals::getSession('user_id'), FILTER_VALIDATE_INT);

        if (!$userId) {
            $this->redirect('/Blog/login');
        }
        $user = $this->userRepository->find($userId);
        if ($user === null) {
            Superglobals::setFlashMessage("danger", "L'utilisateur n'existe pas.");
            $this->redirect('/Blog/user/'. $userId);
        }

        if (!password_verify(Superglobals::getPost('currentPassword'), $user->getPassword())) {
            Superglobals::setFlashMessage("danger", "Le mot de passe actuel est incorrect.");
            $this->redirect('/Blog/user/'. $userId);
        }

        $this->userRepository->delete($userId);
        if ($user->getImage() !== null) {
            $imagePath = UPLOADS_PROFILE_PATH . $user->getImage();
            $this->imageService->deleteImage($imagePath);
        }
        Superglobals::setFlashMessage("success", "Votre compte a été supprimé avec succès.");
        session_destroy();
        $this->redirect('/Blog/');
    }

    /**
     * Update the user profile
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function updateProfile(): void
    {
        if (Superglobals::getServer("REQUEST_METHOD") === "POST") {
            $userId = Superglobals::getSession('user_id', FILTER_VALIDATE_INT);
            $name = trim(htmlspecialchars_decode(Superglobals::getPost('name')));
            $lastName = trim(htmlspecialchars_decode(Superglobals::getPost('lastName')));
            $pseudo = trim(htmlspecialchars_decode(Superglobals::getPost('pseudo')));
            $email = trim(htmlspecialchars_decode(Superglobals::getPost('email')));
            $currentPassword = trim(htmlspecialchars_decode(Superglobals::getPost('currentPassword')));
            $newPassword = trim(htmlspecialchars_decode(Superglobals::getPost('newPassword')));
            $confirmPassword = trim(htmlspecialchars_decode(Superglobals::getPost('confirmPassword')));

            if (empty($name) || empty($lastName) || empty($pseudo) || empty($email)) {
                Superglobals::setFlashMessage("danger", "Tous les champs sont requis.");
                $this->redirect('/Blog/user/'. $userId);
            }
            if ($userId !== null) {
                $user = $this->userRepository->find($userId);

                if ($user !== null && password_verify($currentPassword, $user->getPassword())) {
                    if ($newPassword === $confirmPassword) {
                        $currentImage = $user->getImage();
                        $image = $currentImage;
                        if (Superglobals::getFiles('image')['error'] === 0) {
                            $image = $this->imageService->uploadImage(Superglobals::getFiles('image'), UPLOADS_PROFILE_PATH);
                            if ($image === null) {
                                Superglobals::setFlashMessage("danger", "Erreur lors de l'envoi du fichier.");
                                $this->redirect('/Blog/user/' . $userId);
                            }
                            if ($currentImage && $currentImage !== 'avatar.png') {
                                $currentImagePath = UPLOADS_PROFILE_PATH . $currentImage;
                                if (file_exists($currentImagePath)) {
                                    $this->imageService->deleteImage($currentImagePath);
                                }
                            }
                        }
                        try {
                            $this->userRepository->updateProfile($userId, $name, $image, $lastName, $email, $pseudo);
                            if (!empty($newPassword)) {
                                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                                $this->userRepository->updatePassword($email, $hashedPassword);
                            }
                            Superglobals::setSession('user_name', $name);
                            Superglobals::setSession('user_last_name', $lastName);
                            Superglobals::setSession('user_email', $email);
                            Superglobals::setSession('user_pseudo', $pseudo);
                            Superglobals::setFlashMessage("success", "Vos informations ont été mises à jour avec succès.");
                            $this->redirect('/Blog/user/'. $userId);
                        } catch (Exception $e) {
                            $this->redirect('/Blog/user/'. $userId);
                            Superglobals::setFlashMessage("danger", "pseudo ou email deja utilisé");
                        }
                    } else {
                        Superglobals::setFlashMessage("danger", "Les mots de passe ne correspondent pas.");
                    }
                } else {
                    Superglobals::setFlashMessage("danger", "Le mot de passe actuel est incorrect.");
                    $this->redirect('/Blog/user/'. $userId);
                }
            }
        }
    }
}
	