<?php

namespace App\Repository;

use App\Config\DatabaseConnect;
use App\Models\User;
use DateTime;
use Exception;
use PDO;
use PDOException;

class UserRepository
{
    private \PDO $mysqlClient;

    /**
     * UserRepository constructor.
     */
    public function __construct()
    {
        $this->mysqlClient = DatabaseConnect::connect();
    }

    /**
     * find the latest users
     * @param int $limit
     * @return bool|array
     */
    public function findLatestUsers(int $limit = 3): bool|array
    {
        $sql = 'SELECT * FROM users ORDER BY createdAt DESC LIMIT :limit';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * find all users
     * @throws Exception
     */
    public function findAll(): array
    {
        $sql = 'SELECT * FROM users ORDER BY createdAt DESC';
        $statement = $this->mysqlClient->query($sql);
        $usersData = $statement->fetchAll(PDO::FETCH_ASSOC);
        $users = [];

        foreach ($usersData as $userData) {
            $createdAt = new DateTime($userData['createdAt']);
            $updatedAt = new DateTime($userData['updateAt']);
            $userDataArray = [
                'id' => $userData['id'],
                'name' => $userData['name'],
                'lastName' => $userData['lastName'],
                'image' => $userData['image'],
                'pseudo' => $userData['pseudo'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'role' => $userData['role'],
                'firstLoginDone' => $userData['first_login_done'],
                'resetToken' => $userData['resetToken']
            ];
            $user = new User($userDataArray);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * find user by email
     * @param string $email
     * @return User|null
     * @throws Exception
     */
    public function findByEmail(string $email): ?User
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['email' => $email]);
        $userData = $statement->fetch(PDO::FETCH_ASSOC);
        $firstLoginDone = isset($userData['first_login_done']) ? (bool)$userData['first_login_done'] : null;
        if ($userData) {
            $createdAt = new DateTime($userData['createdAt']);
            $updatedAt = new DateTime($userData['updateAt']);
            $userDataArray = [
                'id' => $userData['id'],
                'name' => $userData['name'],
                'lastName' => $userData['lastName'],
                'image' => $userData['image'],
                'pseudo' => $userData['pseudo'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'role' => $userData['role'],
                'firstLoginDone' => isset($userData['first_login_done']) ? (bool)$userData['first_login_done'] : null,
                'resetToken' => $userData['resetToken']
            ];
            return new User($userDataArray);
        }
        return null;
    }

    /**
     * find user by id
     * @throws Exception
     */
    public function find(int $id): ?User
    {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['id' => $id]);
        $userData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            $createdAt = new DateTime($userData['createdAt']);
            $updatedAt = new DateTime($userData['updateAt']);
            $userDataArray = [
                'id' => $userData['id'],
                'name' => $userData['name'],
                'lastName' => $userData['lastName'],
                'image' => $userData['image'],
                'pseudo' => $userData['pseudo'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'createdAt' => $createdAt,
                'updatedAt' => $updatedAt,
                'role' => $userData['role'],
                'firstLoginDone' => isset($userData['first_login_done']) ? (bool)$userData['first_login_done'] : null,
                'resetToken' => $userData['resetToken']
            ];
            return new User($userDataArray);
        }
        return null;
    }

    /**
     * create a new user
     * @param string $name
     * @param string $lastName
     * @param null $image
     * @param string $pseudo
     * @param string $email
     * @param string $password
     * @param string $role
     * @param string|null $resetToken
     * @throws Exception
     */
    public function createUser(string $name, string $lastName, $image, string $pseudo, string $email, string $password, string $role, ?string $resetToken): void
    {
        try {
            $pdo = DatabaseConnect::connect();
            $stmt = $pdo->prepare("INSERT INTO users (name, lastName, image, pseudo, email, password, role, resetToken) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $lastName, $image, $pseudo, $email, $password, $role, $resetToken]);
        } catch (PDOException $e) {
            if ($e->getCode() == '23000' && str_contains($e->getMessage(), 'unique_email')) {
                throw new Exception("L'adresse e-mail est déjà utilisée.");
            } elseif ($e->getCode() == '23000' && str_contains($e->getMessage(), 'unique_pseudo')) {
                throw new Exception("Le pseudo est déjà utilisé.");
            } else {
                throw new Exception("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
            }
        }
    }

    /**
     * delete a user
     * @param int $id
     * @throws Exception
     */
    public function delete(int $id): void
    {
        try {
            $this->mysqlClient->beginTransaction();
            $stmt = $this->mysqlClient->prepare("
                UPDATE comments
                SET user_id = (SELECT id FROM users WHERE email = 'sylvie.pepete@live.fr' AND role = 'subscriber')
                WHERE user_id = :user_id
            ");
            $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $sql = 'DELETE FROM users WHERE id = :id';
            $statement = $this->mysqlClient->prepare($sql);
            $statement->execute(['id' => $id]);
            $this->mysqlClient->commit();
        } catch (PDOException $e) {
            $this->mysqlClient->rollBack();
            throw new Exception("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
        }
    }

    /**
     * update user profile by user himself
     * @param int $userId
     * @param string $name
     * @param string $image
     * @param string $lastName
     * @param string $email
     * @param string $pseudo
     * @throws Exception
     */
    public function updateProfile(int $userId, string $name, string $image, string $lastName, string $email, string $pseudo): void
    {
        try {
            $sql = 'UPDATE users SET name = :name, lastName = :lastName, image = :image  ,pseudo = :pseudo, email = :email  WHERE id = :id';
            $statement = $this->mysqlClient->prepare($sql);
            $statement->execute([
                'name' => $name,
                'lastName' => $lastName,
                'image' => $image,
                'pseudo' => $pseudo,
                'email' => $email,
                'id' => $userId,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du profil : " . $e->getMessage());
        }
    }

    /**
     * update user profile by admin
     * @param int $userId
     * @param string $name
     * @param string $lastName
     * @param string $email
     * @param string $pseudo
     * @param string $role
     */
    public function updateProfileByAdmin(int $userId, string $name, string $lastName, string $email, string $pseudo, string $role): void
    {
        $sql = 'UPDATE users SET name = :name, lastName = :lastName, pseudo = :pseudo, email = :email, role = :role WHERE id = :id';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute([
            'name' => $name,
            'lastName' => $lastName,
            'pseudo' => $pseudo,
            'email' => $email,
            'role' => $role,
            'id' => $userId,
        ]);
    }


    /**
     * update user password
     * @param $email
     * @param $resetToken
     */
    public function setResetToken($email, $resetToken): void
    {
        $sql = 'UPDATE users SET resetToken = :resetToken WHERE email = :email';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute([
            'email' => $email,
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * find user by reset token
     * @param string $resetToken
     * @return User|null
     * @throws Exception
     */
    public function findByResetToken(string $resetToken): ?User
    {
        $sql = 'SELECT * FROM users WHERE resetToken = :resetToken';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute(['resetToken' => $resetToken]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $userDataArray = [
            'id' => $result['id'],
            'name' => $result['name'],
            'lastName' => $result['lastName'],
            'image' => $result['image'],
            'pseudo' => $result['pseudo'],
            'email' => $result['email'],
            'password' => $result['password'],
            'createdAt' => new DateTime($result['createdAt']),
            'updatedAt' => new DateTime($result['updateAt']),
            'role' => $result['role'],
            'firstLoginDone' => isset($result['first_login_done']) ? (bool)$result['first_login_done'] : null,
            'resetToken' => $result['resetToken']
        ];

        $user = new User($userDataArray);
        return $user;
    }

    /**
     * update user password
     * @param string $userEmail
     * @param string $password_hash
     * @throws Exception
     */
    public function updatePassword(string $userEmail, string $password_hash): void
    {
        try {
            $sql = 'UPDATE users SET password = :password WHERE email = :email';
            $statement = $this->mysqlClient->prepare($sql);
            $statement->execute([
                'email' => $userEmail,
                'password' => $password_hash,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du mot de passe : " . $e->getMessage());
        }
    }

    /**
     * update first login done
     * @param int $userId
     * @param bool $value
     */
    public function updateFirstLoginDone(int $userId, bool $value): void
    {
        $sql = 'UPDATE users SET first_login_done = :value WHERE id = :userId';
        $statement = $this->mysqlClient->prepare($sql);
        $statement->execute([
            'value' => $value,
            'userId' => $userId,
        ]);
    }
}
	