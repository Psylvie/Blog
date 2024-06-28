<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Services\ImageService;
use App\Utils\CsrfProtection;
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
        $csrfToken = CsrfProtection::generateToken();
        Superglobals::setSession('csrfToken', $csrfToken);
        $user = $this->userRepository->find($userId);
        $this->render('Admin/adminUpdateUserProfile.html.twig', ['user' => $user, 'csrfToken' => $csrfToken]);
    }

    /**
     * delete user by admin
     * @param $userId
     * @throws Exception
     */
    public function delete($userId)
    {
        try {
            if ($userId == 87) {
                throw new Exception("Vous ne pouvez pas supprimer l'utilisateur anonyme.");
            }
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
            $csrfToken = Superglobals::getPost('csrfToken');
            if (!CsrfProtection::checkToken($csrfToken)) {
                Superglobals::setFlashMessage("danger", "Erreur de validation CSRF !");
                $this->redirect('/Blog/admin/users/list');
            }
            $name = $this->testInput(Superglobals::getPost('name'));
            $lastName = $this->testInput(Superglobals::getPost('lastName'));
            $email = $this->testInput(Superglobals::getPost('email'));
            $pseudo = $this->testInput(Superglobals::getPost('pseudo'));
            $role = $this->testInput(Superglobals::getPost('role'));
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
            $csrfToken = Superglobals::getPost("csrfToken");
            if (!CsrfProtection::checkToken($csrfToken)) {
                Superglobals::setFlashMessage("danger", "Erreur de validation CSRF !");
                $this->redirect('/Blog/admin/users/create');
            }
            $name = $this->testInput(Superglobals::getPost("name"));
            $lastName = $this->testInput(Superglobals::getPost("lastName"));
            $pseudo = $this->testInput(Superglobals::getPost("pseudo"));
            $email = $this->testInput(Superglobals::getPost("email"));
            $role = $this->testInput(Superglobals::getPost("role"));
            $resetToken = $this->testInput(Superglobals::getPost("resetToken"));
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
	