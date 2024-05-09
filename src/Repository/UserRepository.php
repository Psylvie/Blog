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
					$userData['slug'],
					$userData['email'],
					$userData['password'],
					$createdAt,
					$updatedAt,
					$userData['role'],
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
			
			if ($userData) {
				$createdAt = new DateTime($userData['createdAt']);
				$updatedAt = new DateTime($userData['updateAt']);
				return new User(
					$userData['id'],
					$userData['name'],
					$userData['lastName'],
					$userData['image'],
					$userData['slug'],
					$userData['email'],
					$userData['password'],
					$createdAt,
					$updatedAt,
					$userData['role'],
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
					$userData['slug'],
					$userData['email'],
					$userData['password'],
					$createdAt,
					$updatedAt,
					$userData['role'],
					$userData['resetToken']
				);
			}
			
			return null;
		}
		
		/**
		 * @throws Exception
		 */
		public function createUser($name, $lastName, $image, $slug, $email, $password, $role, $resetToken): void
		{
			try {
				$pdo = DatabaseConnect::connect();
				$stmt = $pdo->prepare("INSERT INTO users (name, lastName, image, slug, email, password, role, resetToken) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
				$stmt->execute([$name, $lastName, $image, $slug, $email, $password, $role, $resetToken]);
			} catch (PDOException $e) {
				if ($e->getCode() == '23000' && strpos($e->getMessage(), 'unique_email') !== false) {
					throw new Exception("L'adresse e-mail est déjà utilisée.");
				} else {
					throw new Exception("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
				}
			}
		}
		
		
		public function delete(int $id): void
		{
			$sql = 'DELETE FROM users WHERE id = :id';
			$statement = $this->mysqlClient->prepare($sql);
			$statement->execute(['id' => $id]);
		}
		
		public function updateProfile($userId, $name, $lastName, $email, $role): void
		{
			$sql = 'UPDATE users SET name = :name, lastName = :lastName, email = :email, role = :role WHERE id = :id';
			$statement = $this->mysqlClient->prepare($sql);
			$statement->execute([
				'id' => $userId,
				'name' => $name,
				'lastName' => $lastName,
				'email' => $email,
				'role' => $role,
			]);
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
				$result['slug'],
				$result['email'],
				$result['password'],
				new DateTime($result['createdAt']),
				new DateTime($result['updateAt']),
				$result['role'],
				$result['resetToken']
			
			);
			
			return $user;
		}
		
		public function updatePassword(string $userEmail, string $password_hash): void
		{
			
			$sql = 'UPDATE users SET password = :password WHERE email = :email';
			$statement = $this->mysqlClient->prepare($sql);
			$statement->execute([
				'email' => $userEmail,
				'password' => $password_hash,
			])
			;
		}
		
		
	}