<?php
	
	namespace App\Models;
	
	
	use DateTime;
	/**
	 * @uniqueEntity(fields="email"), message="Cet email est déjà utilisé."
	 
	 */
	class User
	{
		private int $id;
		private string $name;
		private string $lastName;
		private string $image;
		private string $slug;
		private string $email;
		private string $password;
		private datetime $createdAt;
		private datetime $updateAt;
		private string $role;
		private ?string $resetToken;
		
		/**
		 * @param int $id
		 * @param string $name
		 * @param string $lastName
		 * @param string $image
		 * @param string $slug
		 * @param string $email
		 * @param string $password
		 * @param DateTime $createdAt
		 * @param DateTime $updateAt
		 * @param string $role
		 * @param string|null $resetToken
		 */
		public function __construct(int $id, string $name, string $lastName, string $image, string $slug, string $email, string $password, DateTime $createdAt, DateTime $updateAt, string $role, ?string $resetToken)
		{
			$this->id = $id;
			$this->name = $name;
			$this->lastName = $lastName;
			$this->image = $image;
			$this->slug = $slug;
			$this->email = $email;
			$this->password = $password;
			$this->createdAt = $createdAt;
			$this->updateAt = $updateAt;
			$this->role = $role;
			$this->resetToken = $resetToken;
		}
		
		/**
		 * @return int
		 */
		public function getId(): int
		{
			return $this->id;
		}
		
		/**
		 * @param int $id
		 */
		public function setId(int $id): void
		{
			$this->id = $id;
		}
		
		/**
		 * @return string
		 */
		public function getName(): string
		{
			return $this->name;
		}
		
		/**
		 * @param string $name
		 */
		public function setName(string $name): void
		{
			$this->name = $name;
		}
		
		/**
		 * @return string
		 */
		public function getLastName(): string
		{
			return $this->lastName;
		}
		
		/**
		 * @param string $lastName
		 */
		public function setLastName(string $lastName): void
		{
			$this->lastName = $lastName;
		}
		
		/**
		 * @return string
		 */
		public function getImage(): string
		{
			return $this->image;
		}
		
		/**
		 * @param string $image
		 */
		public function setImage(string $image): void
		{
			$this->image = $image;
		}
		
		/**
		 * @return string
		 */
		public function getSlug(): string
		{
			return $this->slug;
		}
		
		/**
		 * @param string $slug
		 */
		public function setSlug(string $slug): void
		{
			$this->slug = $slug;
		}
		
		/**
		 * @return string
		 */
		public function getEmail(): string
		{
			return $this->email;
		}
		
		/**
		 * @param string $email
		 */
		public function setEmail(string $email): void
		{
			$this->email = $email;
		}
		
		/**
		 * @return string
		 */
		public function getPassword(): string
		{
			return $this->password;
		}
		
		/**
		 * @param string $password
		 */
		public function setPassword(string $password): void
		{
			$this->password = $password;
		}
		
		/**
		 * @return DateTime
		 */
		public function getCreatedAt(): DateTime
		{
			return $this->createdAt;
		}
		
		/**
		 * @param DateTime $createdAt
		 */
		public function setCreatedAt(DateTime $createdAt): void
		{
			$this->createdAt = $createdAt;
		}
		
		/**
		 * @return DateTime
		 */
		public function getUpdateAt(): DateTime
		{
			return $this->updateAt;
		}
		
		/**
		 * @param DateTime $updateAt
		 */
		public function setUpdateAt(DateTime $updateAt): void
		{
			$this->updateAt = $updateAt;
		}
		
		/**
		 * @return string
		 */
		public function getRole(): string
		{
			return $this->role;
		}
		
		/**
		 * @param string $role
		 */
		public function setRole(string $role): void
		{
			$this->role = $role;
		}
		
		/**
		 * @return string|null
		 */
		public function getResetToken(): ?string
		{
			return $this->resetToken;
		}
		
		/**
		 * @param string|null $resetToken
		 */
		public function setResetToken(?string $resetToken): void
		{
			$this->resetToken = $resetToken;
		}
		
	}
	