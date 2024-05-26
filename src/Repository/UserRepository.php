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
		
		public function __construct()
		{
			$this->mysqlClient = DatabaseConnect::connect();
		}
		
		public function findLatestUsers($limit = 3): bool|array
		{
			$sql = 'SELECT * FROM users ORDER BY createdAt DESC LIMIT :limit';
			$statement = $this->mysqlClient->prepare($sql);
			$statement->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
			$statement->execute();
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		
		/**
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
				$users[] = new User(
					$userData['id'],
					$userData['name'],
					$userData['lastName'],
					$userData['image'],
					$userData['pseudo'],
					$userData['email'],
					$userData['password'],
					$createdAt,
					$updatedAt,
					$userData['role'],
					$userData['first_login_done'],
					$userData['resetToken']
				);
			}
			
			return $users;
		}
		/**
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
				return new User(
					$userData['id'],
					$userData['name'],
					$userData['lastName'],
					$userData['image'],
					$userData['pseudo'],
					$userData['email'],
					$userData['password'],
					$createdAt,
					$updatedAt,
					$userData['role'],
					$firstLoginDone,
					$userData['resetToken']
				);
			}
			return null;
		}
		
		/**
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
				return new User(
					$userData['id'],
					$userData['name'],
					$userData['lastName'],
					$userData['image'],
					$userData['pseudo'],
					$userData['email'],
					$userData['password'],
					$createdAt,
					$updatedAt,
					$userData['role'],
					$userData['first_login_done'],
					$userData['resetToken']
				);
			}
			return null;
		}
		
		/**
		 * @throws Exception
		 */
		public function createUser($name, $lastName, $image, $pseudo, $email, $password, $role, $resetToken): void
		{
			try {
				$pdo = DatabaseConnect::connect();
				$stmt = $pdo->prepare("INSERT INTO users (name, lastName, image, pseudo, email, password, role, resetToken) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");				$stmt->execute([$name, $lastName, $image, $pseudo, $email, $password, $role, $resetToken]);
			} catch (PDOException $e) {
				if ($e->getCode() == '23000' && strpos($e->getMessage(), 'unique_email') !== false) {
					throw new Exception("L'adresse e-mail est déjà utilisée.");
				} else {
					throw new Exception("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
				}
			}
		}
		
		/**
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
		 * @throws Exception
		 */
		public function updateProfile($userId, $name, $image, $lastName, $email, $pseudo, $role): void
		{
			try {
				$sql = 'UPDATE users SET name = :name, lastName = :lastName, image = :image  ,pseudo = :pseudo, email = :email, role = :role WHERE id = :id';
				$statement = $this->mysqlClient->prepare($sql);
				$statement->execute([
					'name' => $name,
					'lastName' => $lastName,
					'image' => $image,
					'pseudo' => $pseudo,
					'email' => $email,
					'role' => $role,
					'id' => $userId,
				]);
			} catch (PDOException $e) {
				throw new Exception("Erreur lors de la mise à jour du profil : " . $e->getMessage());
			}
		}
		
		
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
			
			$user = new User(
				$result['id'],
				$result['name'],
				$result['lastName'],
				$result['image'],
				$result['pseudo'],
				$result['email'],
				$result['password'],
				new DateTime($result['createdAt']),
				new DateTime($result['updateAt']),
				$result['role'],
				$result['first_login_done'],
				$result['resetToken']
			);
			return $user;
		}
		
		/**
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
	