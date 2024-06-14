<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Services\ImageService;
use App\Utils\Superglobals;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

include __DIR__ . '/../../config/Config.php';

/**
 * Class AdminUserController
 * @package App\Controllers\Admin
 */
class AdminUserController extends Controller
{
    protected ImageService $imageService;


    public function __construct()
    {
        parent::__construct();
        $this->imageService = new ImageService();
    }

    /**
     * show all users
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function list()
    {
        $users = $this->userRepository->findAll();
        $this->render('Admin/adminUsersList.html.twig', ['users' => $users]);
    }

    /**
     * show user profile
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function show($userId)
    {
        $user = $this->userRepository->find($userId);
        $this->render('Admin/adminUpdateUserProfile.html.twig', ['user' => $user]);
    }

    /**
     * delete user by admin
     * @param $userId
     * @throws Exception
     */
    public function delete($userId)
    {
        try {
            $this->userRepository->delete($userId);
            $user = $this->userRepository->find($userId);
            if ($user && $user->getImage()) {
                $this->imageService->deleteImage(UPLOADS_PROFILE_PATH . $user->getImage());
            }
            Superglobals::setFlashMessage('success', "L'utilisateur $userId a été supprimé avec succès.");
        } catch (Exception $e) {
            Superglobals::setFlashMessage('danger', "Une erreur s'est produite lors de la suppression de l'utilisateur.");
        }
        $this->redirect('/Blog/admin/users/list');
    }

    /**
     * update user profile by admin
     * @param $userId
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
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
                $this->redirect('/Blog/admin/users/list');
            }
            try {
                $this->userRepository->updateProfileByAdmin($userId, $name, $lastName, $email, $pseudo, $role);
                Superglobals::setFlashMessage("success", "Les informations de l'utilisateur $name $lastName ont été mises à jour avec succès.");
            } catch (Exception $e) {
                Superglobals::setFlashMessage("danger", "Une erreur s'est produite lors de la mise à jour des informations de l'utilisateur.");
            }
            $this->redirect('/Blog/admin/users/list');
        }
    }


    /**
     * create user form
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function createUser()
    {
        $this->render('Admin/adminCreateUser.html.twig');
    }

    /**
     * create user process
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
                $this->redirect('/Blog/admin/users/create');
            }
            try {
                $this->userRepository->createUser($name, $lastName, $image, $pseudo, $email, $hashedPassword, $role, $resetToken);
                Superglobals::setFlashMessage("success", "L'utilisateur a été créé avec succès !");
                $this->redirect('/Blog/admin/users/list');
            } catch (Exception $e) {
                Superglobals::setFlashMessage("danger", "Une erreur s'est produite lors de la création de l'utilisateur : " . $e->getMessage());
                $this->redirect('/Blog/admin/users/create');
            }
        }
    }
}
	