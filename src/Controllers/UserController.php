<?php

namespace App\Controllers;

use App\Repository\UserRepository;
use App\Utils\Superglobals;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

include __DIR__ . '/../Config/Config.php';


class UserController extends Controller
{

    private UserRepository $userRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function show($userId)
    {
        $user = $this->userRepository->find($userId);
        $this->render('Users/user.html.twig', ['user' => $user]);
    }

    /**
     * @throws Exception
     */
    #[NoReturn] public function deleteUser($userId): void
    {
        $userId = filter_var(Superglobals::getSession('user_id'), FILTER_VALIDATE_INT);

        if (!$userId) {
            header("Location: /Blog/login");
            exit();
        }
        $user = $this->userRepository->find($userId);
        if ($user === null) {
            Superglobals::setFlashMessage("danger", "L'utilisateur n'existe pas.");
            header("Location: /Blog/user/{$userId}");
            exit();
        }

        if (!password_verify($_POST['currentPassword'], $user->getPassword())) {
            Superglobals::setFlashMessage("danger", "Le mot de passe actuel est incorrect.");
            header("Location: /Blog/user/{$userId}");
            exit();
        }

        $this->userRepository->delete($userId);
        if ($user->getImage() !== null) {
            unlink(UPLOADS_PROFILE_PATH . $user->getImage());
        }
        Superglobals::setFlashMessage("success", "Votre compte a été supprimé avec succès.");
        session_destroy();
        header("Location: /Blog/");
        exit();
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function updateProfile(): void
    {
        if (Superglobals::getServer("REQUEST_METHOD") === "POST") {
            $userId = Superglobals::getSession('user_id', FILTER_VALIDATE_INT);
            $name = trim(htmlspecialchars(Superglobals::getPost('name')));
            $lastName = trim(htmlspecialchars(Superglobals::getPost('lastName')));
            $pseudo = trim(htmlspecialchars(Superglobals::getPost('pseudo')));
            $email = trim(htmlspecialchars(Superglobals::getPost('email')));
            $currentPassword = trim(htmlspecialchars(Superglobals::getPost('currentPassword')));
            $newPassword = trim(htmlspecialchars(Superglobals::getPost('newPassword')));
            $confirmPassword = trim(htmlspecialchars(Superglobals::getPost('confirmPassword')));

            if ($userId !== null) {
                $user = $this->userRepository->find($userId);

                if ($user !== null && password_verify($currentPassword, $user->getPassword())) {
                    if ($newPassword === $confirmPassword) {
                        $image = $user->getImage();
                        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                            $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                            $filename = $_FILES['image']['name'];
                            $filetype = $_FILES['image']['type'];
                            $filesize = $_FILES['image']['size'];

                            $extension = pathinfo($filename, PATHINFO_EXTENSION);
                            if (!array_key_exists($extension, $allowed) || !in_array($filetype, $allowed)) {
                                Superglobals::setFlashMessage("danger", "Le type de fichier n'est pas autorisé.");
                            }
                            if ($filesize > 1024 * 1024) {
                                Superglobals::setFlashMessage("danger", "Le fichier est trop volumineux.");
                            }
                            $newname = md5(uniqid());
                            $newfilename = UPLOADS_PROFILE_PATH . $newname . '.' . $extension;
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $newfilename)) {
                                if ($image !== 'avatar.png') {
                                    $oldImage = $image;
                                    if ($oldImage !== null) {
                                        unlink(UPLOADS_PROFILE_PATH . $oldImage);
                                    }
                                }
                                $image = $newname . '.' . $extension;
                            } else {
                                Superglobals::setFlashMessage("danger", "Une erreur est survenue lors de l'envoi du fichier.");
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
                            header("Location: /Blog/user/{$userId}");
                            exit();
                        } catch (Exception $e) {
                            header("Location: /Blog/user/{$userId}");
                            Superglobals::setFlashMessage("danger", "pseudo ou email deja utilisé");
                        }
                    } else {
                        Superglobals::setFlashMessage("danger", "Les mots de passe ne correspondent pas.");
                    }
                } else {
                    Superglobals::setFlashMessage("danger", "Le mot de passe actuel est incorrect.");
                    header("Location: /Blog/user/{$userId}");
                }
            }
        }
    }
}
	