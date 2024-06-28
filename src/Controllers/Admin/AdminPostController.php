<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Services\ImageService;
use App\Utils\CsrfProtection;
use App\Utils\Superglobals;
use Exception;
use PDOException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

include __DIR__ . '/../../config/Config.php';

/**
 * Class AdminPostController
 * @package App\Controllers\Admin
 */
class AdminPostController extends Controller
{

    protected ImageService $imageService;

    /**
     * AdminPostController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->imageService = new ImageService();
    }

    /**
     * show all posts
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function showPost()
    {
        $posts = $this->postRepository->getAllPosts();
        $this->render('Admin/adminShowPost.html.twig', ['posts' => $posts]);
    }

    /**
     * form to create a new post
     * @throws Exception
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function newPost()
    {
        $csrfToken = CsrfProtection::generateToken();
        $this->render('Admin/adminFormPost.html.twig', ['csrfToken' => $csrfToken]);
    }

    /**
     * create a new post
     * @throws Exception
     */
    public function createPost()
    {
        $requestMethod = Superglobals::getServer('REQUEST_METHOD');

        if ($requestMethod === 'POST') {
            $csrfToken = Superglobals::getPost('csrfToken');
            if (!CsrfProtection::checkToken($csrfToken)) {
                Superglobals::setFlashMessage("danger", "Token CSRF invalide");
                $this->redirect('/Blog/admin/newPost');
            }
            $title =$this->testInput(Superglobals::getPost('title') ?? '');
            $chapo = $this->testInput(Superglobals::getPost('chapo') ?? '');
            $author = $this->testInput(Superglobals::getPost('author') ?? '');
            $content = $this->testInput(Superglobals::getPost('content') ?? '');
            $published = filter_var(Superglobals::getPost('published'), FILTER_VALIDATE_INT) ?? 0;
            $userId = Superglobals::getSession('user_id');
            $image = null;

            if (Superglobals::getFiles('image')['error'] === 0) {
                $image = $this->imageService->uploadImage(Superglobals::getFiles('image'), UPLOADS_POST_PATH);
                if ($image === null) {
                    Superglobals::setFlashMessage("danger", "Erreur lors de l'envoi du fichier");
                    $this->redirect('/Blog/admin/newPost');
                }
            }
            if (empty($title) || empty($chapo) || empty($author) || empty($content)) {
                Superglobals::setFlashMessage("danger", "Tous les champs sont requis.");
                $this->redirect('/Blog/admin/newPost');
            }
            try {
                $postRepository = $this->postRepository;
                $postRepository->createPost($title, $chapo, $author, $content, $image, $userId, $published);
                Superglobals::setFlashMessage("success", "Le post a été créé avec succès !");
                $this->redirect('/Blog/admin/showPost');
            } catch (PDOException $e) {
                Superglobals::setFlashMessage("danger", "Une erreur s'est produite lors de la création du post : " . $e->getMessage());
                $this->redirect('/Blog/admin/newPost');
            }
        } else {
            $this->redirect('/Blog/admin/newPost');
        }
    }

    /**
     * delete a post by id and delete comments associated with the post
     * @throws Exception
     */
    public function deletePost($postId): void
    {
        try {
            $post = $this->postRepository->getPostById($postId);
            $imageName = $post?->getImage();
            $this->postRepository->deletePost($postId);
            if ($imageName !== null) {
                $imagePath = UPLOADS_POST_PATH . $imageName;
                $this->imageService->deleteImage($imagePath);
                }
            Superglobals::setFlashMessage("success", "Le post a été supprimé avec succès !");
            $this->redirect('/Blog/admin/showPost');
        } catch (PDOException $e) {
            Superglobals::setFlashMessage("danger", "Une erreur s'est produite lors de la suppression du post : " . $e->getMessage());
            $this->redirect('/Blog/admin/showPost');
        }
    }

    /**
     * form to update a post
     * @param int $postId
     * @throws Exception
     */
    public function updatePost(int $postId)
    {
        if (Superglobals::getServer('REQUEST_METHOD') === 'POST') {
            $csrfToken = Superglobals::getPost('csrfToken');
            if (!CsrfProtection::checkToken($csrfToken)) {
                Superglobals::setFlashMessage("danger", "Token CSRF invalide");
                $this->redirect('/Blog/admin/showPost');
            }
            $title = $this->testInput(Superglobals::getPost('title') ?? '');
            $chapo = $this->testInput(Superglobals::getPost('chapo') ?? '');
            $author = $this->testInput(Superglobals::getPost('author') ?? '');
            $content = $this->testInput(Superglobals::getPost('content') ?? '');
            $published = filter_var(Superglobals::getPost('published'), FILTER_VALIDATE_INT) ?? 0;

            if (empty($title) || empty($chapo) || empty($author) || empty($content)) {
                throw new Exception("Tous les champs sont requis.");
            }
            $post = $this->postRepository->getPostById($postId);
            $currentImage = $post->getImage();
            $image = $currentImage;

            if (Superglobals::getFiles('image')['error'] === 0) {
                $image = $this->imageService->uploadImage(Superglobals::getFiles('image'), UPLOADS_POST_PATH);
                if ($image === null) {
                    Superglobals::setFlashMessage("danger", "Erreur lors de l'envoi du fichier");
                    $this->redirect('/Blog/admin/showPost');
                }
                if ($currentImage && $currentImage !== 'defaultImage.jpg') {
                    $currentImagePath = UPLOADS_POST_PATH . $currentImage;
                    if (file_exists($currentImagePath)) {
                        unlink($currentImagePath);
                    }
                }
            }
            try {
                $this->postRepository->updatePost($postId, $title, $chapo, $author, $content, $image, $published);
                Superglobals::setFlashMessage("success", "Le post a été mis à jour avec succès !");
            } catch (\PDOException $e) {
                throw new Exception("Erreur de connexion à la base de données : " . $e->getMessage());
            }
            $this->redirect('/Blog/admin/showPost');
        } else {
            $post = $this->postRepository->getPostById($postId);
            $this->render('Admin/adminUpdatePost.html.twig', ['post' => $post]);
        }
    }
}
	