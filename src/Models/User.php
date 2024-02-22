<?php
	
	namespace App\Models;
	
	
	class User{
	private ?int $id;
	private ?string $name = null ;
	private ?string $lastName = null;
	private ?string $profilePicture = null;
	private ?string $slug = null;
	private ?string $email = null;
	private ?string $password = null;
	private ?\DateTime $createdAt = null;
	private ?\DateTime $updatedAt = null;
	private ?string $role = null;
		
		/**
		 * @param int|null $id
		 * @param string|null $name
		 * @param string|null $lastName
		 * @param string|null $profilePicture
		 * @param string|null $slug
		 * @param string|null $email
		 * @param string|null $password
		 * @param \DateTime|null $createdAt
		 * @param \DateTime|null $updatedAt
		 * @param string|null $role
		 */
		public function __construct(?int $id, ?string $name, ?string $lastName, ?string $profilePicture, ?string $slug, ?string $email, ?string $password, ?\DateTime $createdAt, ?\DateTime $updatedAt, ?string $role)
		{
			$this->id = $id;
			$this->name = $name;
			$this->lastName = $lastName;
			$this->profilePicture = $profilePicture;
			$this->slug = $slug;
			$this->email = $email;
			$this->password = $password;
			$this->createdAt = $createdAt;
			$this->updatedAt = $updatedAt;
			$this->role = $role;
		}
		
		/**
		 * @return int|null
		 */
		public function getId(): ?int
		{
			return $this->id;
		}
		
		/**
		 * @param int|null $id
		 */
		public function setId(?int $id): void
		{
			$this->id = $id;
		}
		
		/**
		 * @return string|null
		 */
		public function getName(): ?string
		{
			return $this->name;
		}
		
		/**
		 * @param string|null $name
		 */
		public function setName(?string $name): void
		{
			$this->name = $name;
		}
		
		/**
		 * @return string|null
		 */
		public function getLastName(): ?string
		{
			return $this->lastName;
		}
		
		/**
		 * @param string|null $lastName
		 */
		public function setLastName(?string $lastName): void
		{
			$this->lastName = $lastName;
		}
		
		/**
		 * @return string|null
		 */
		public function getProfilePicture(): ?string
		{
			return $this->profilePicture;
		}
		
		/**
		 * @param string|null $profilePicture
		 */
		public function setProfilePicture(?string $profilePicture): void
		{
			$this->profilePicture = $profilePicture;
		}
		
		/**
		 * @return string|null
		 */
		public function getSlug(): ?string
		{
			return $this->slug;
		}
		
		/**
		 * @param string|null $slug
		 */
		public function setSlug(?string $slug): void
		{
			$this->slug = $slug;
		}
		
		/**
		 * @return string|null
		 */
		public function getEmail(): ?string
		{
			return $this->email;
		}
		
		/**
		 * @param string|null $email
		 */
		public function setEmail(?string $email): void
		{
			$this->email = $email;
		}
		
		/**
		 * @return string|null
		 */
		public function getPassword(): ?string
		{
			return $this->password;
		}
		
		/**
		 * @param string|null $password
		 */
		public function setPassword(?string $password): void
		{
			$this->password = $password;
		}
		
		/**
		 * @return \DateTime|null
		 */
		public function getCreatedAt(): ?\DateTime
		{
			return $this->createdAt;
		}
		
		/**
		 * @param \DateTime|null $createdAt
		 */
		public function setCreatedAt(?\DateTime $createdAt): void
		{
			$this->createdAt = $createdAt;
		}
		
		/**
		 * @return \DateTime|null
		 */
		public function getUpdatedAt(): ?\DateTime
		{
			return $this->updatedAt;
		}
		
		/**
		 * @param \DateTime|null $updatedAt
		 */
		public function setUpdatedAt(?\DateTime $updatedAt): void
		{
			$this->updatedAt = $updatedAt;
		}
		
		/**
		 * @return string|null
		 */
		public function getRole(): ?string
		{
			return $this->role;
		}
		
		/**
		 * @param string|null $role
		 */
		public function setRole(?string $role): void
		{
			$this->role = $role;
		}
		
		
	}