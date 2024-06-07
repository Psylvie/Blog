<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Repository\UserRepository;
use App\Utils\Superglobals;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

include __DIR__ . '/../../config/Config.php';

class AdminUserController extends Controller
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
     * @throws \Exception
     */
    public function list()
    {
        $users = $this->userRepository->findAll();
        $this->render('Admin/adminUsersList.html.twig', ['users' => $users]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws \Exception
     */
    public function show($userId)
    {
        $user = $this->userRepository->find($userId);
        $this->render('Admin/adminUpdateUserProfile.html.twig', ['user' => $user]);
    }

    public function delete($userId)
    {
        try {
            $this->userRepository->delete($userId);
            $user = $this->userRepository->find($userId);
            if ($user && $user->getImage()) {
                unlink(UPLOADS_PROFILE_PATH . $user->getImage());
            }
            Superglobals::setFlashMessage('success', "L'utilisateur $userId a été supprimé avec succès.");
        } catch (Exception $e) {
            Superglobals::setFlashMessage('danger', "Une erreur s'est produite lors de la suppression de l'utilisateur.");
        }
        header("Location: /Blog/admin/users/list");
        exit();
    }

    /**
     * @throws Exception
     */
    public function update($userId)
    {
        if (Superglobals::getServer("REQUEST_METHOD") === "POST") {
            $name = Superglobals::getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $lastName = Superglobals::getPost('lastName', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $email = Superglobals::getPost('email', FILTER_VALIDATE_EMAIL) ?? '';
            $pseudo = Superglobals::getPost('pseudo', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $role = Superglobals::getPost('role', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $user = $this->userRepository->find($userId);
            if ($user === null) {
                Superglobals::setFlashMessage("danger", "L'utilisateur n'existe pas.");
                header("Location: /Blog/admin/users/list");
                exit();
            }

            $image = $user->getImage();
            if (Superglobals::getFiles('image')['error'] === 0) {
                $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $filename = Superglobals::getFiles('image')['name'];
                $filetype = Superglobals::getFiles('image')['type'];
                $filesize = Superglobals::getFiles('image')['size'];

                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                if (!array_key_exists($extension, $allowed) || !in_array($filetype, $allowed)) {
                    Superglobals::setFlashMessage("danger", "Erreur de type de fichier");
                } elseif ($filesize > 1024 * 1024) {
                    Superglobals::setFlashMessage("danger", "Erreur de taille de fichier");
                } else {
                    $newname = md5(uniqid());
                    $newfilename = UPLOADS_PROFILE_PATH . $newname . '.' . $extension;
                    if (move_uploaded_file(Superglobals::getFiles('image')['tmp_name'], $newfilename)) {
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
            }

            try {
                $this->userRepository->updateProfileByAdmin($userId, $name, $lastName, $email, $pseudo, $role, $image);
                Superglobals::setFlashMessage("success", "Les informations de l'utilisateur $name $lastName ont été mises à jour avec succès.");
            } catch (Exception $e) {
                Superglobals::setFlashMessage("danger", "Une erreur s'est produite lors de la mise à jour des informations de l'utilisateur.");
            }
            header("Location: /Blog/admin/users/list");
            exit();
        }
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function createUser()
    {

        $this->render('Admin/adminCreateUser.html.twig');
    }

    /**
     * @throws Exception
     */
    public function createUserProcess()
    {
        if (Superglobals::getServer("REQUEST_METHOD") == "POST") {
            $name = htmlspecialchars(trim(Superglobals::getPost("name")));
            $lastName = htmlspecialchars(trim(Superglobals::getPost("lastName")));
            $pseudo = htmlspecialchars(trim(Superglobals::getPost("pseudo")));
            $email = htmlspecialchars(trim(Superglobals::getPost("email")));
            $role = htmlspecialchars(trim(Superglobals::getPost("role")));
            $resetToken = htmlspecialchars(trim(Superglobals::getPost("resetToken")));
            $hashedPassword = password_hash(Superglobals::getPost("password"), PASSWORD_DEFAULT);
            $image = null;
            $firstLoginDone = false;

            $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
            if (!preg_match($pattern, Superglobals::getPost("password"))) {
                Superglobals::setFlashMessage("danger", "Le mot de passe doit contenir au moins 8 caractères, dont au moins une lettre majuscule, une lettre minuscule et un chiffre.");
                header("Location: /Blog/admin/users/create");
                exit();
            }

            if (Superglobals::getFiles('image')['error'] === 0) {
                $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $filename = Superglobals::getFiles('image')['name'];
                $filetype = Superglobals::getFiles('image')['type'];
                $filesize = Superglobals::getFiles('image')['size'];

                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                if (!array_key_exists($extension, $allowed) || !in_array($filetype, $allowed)) {
                    Superglobals::setFlashMessage("danger", "Erreur de type de fichier");
                }
                if ($filesize > 1024 * 1024) {
                    Superglobals::setFlashMessage("danger", "Erreur de taille de fichier");
                }
                $newname = md5(uniqid());
                $newfilename = UPLOADS_PROFILE_PATH . $newname . '.' . $extension;
                move_uploaded_file(Superglobals::getFiles('image')['tmp_name'], $newfilename);
                $image = $newname . '.' . $extension;
            }
            try {
                $userRepository = new UserRepository();
                $userRepository->createUser($name, $lastName, $image, $pseudo, $email, $hashedPassword, $role, $resetToken);
                Superglobals::setFlashMessage("success", "L'utilisateur a été créé avec succès !");
                header("Location: /Blog/admin/users/list");
                exit();
            } catch (Exception $e) {
                Superglobals::setFlashMessage("danger", "Une erreur s'est produite lors de la création de l'utilisateur : " . $e->getMessage());
                header("Location: /Blog/admin/users/create");
                exit();
            }
        }
    }
}
	