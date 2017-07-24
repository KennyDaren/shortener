<?php

namespace Shortener\Domain\User;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shortener\Application\Doctrine\Entity\IdentifiedStatusEntity;

/**
 * User entity
 *
 * @ORM\Entity(repositoryClass="Shortener\Domain\User\UserRepository")
 * @ORM\Table(name="sh_users", indexes={
 *      @ORM\Index(name="users_email_ix", columns={"email"}),
 *      @ORM\Index(name="users_status_ix", columns={"status"})
 * })
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Domain\Users
 */
class UserEntity extends IdentifiedStatusEntity
{
	/**
	 * @ORM\Column(type="string", length=50, name="username")
	 * @var string
	 */
	protected $username;

	/**
	 * @ORM\Column(type="string", length=100, name="email")
	 * @var string
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string", name="email_to_change", nullable=true)
	 * @var string
	 */
	protected $emailToChange = NULL;

	/**
	 * @ORM\Column(type="string", length=255, name="password", nullable=true)
	 * @var string
	 */
	protected $password = NULL;

	/**
	 * @ORM\Column(type="datetime", name="created_at")
	 * @var DateTime
	 */
	protected $createdAt;

	/**
	 * @ORM\Column(type="datetime", name="updated_at", nullable=true)
	 * @var DateTime
	 */
	protected $updatedAt = NULL;

	/**
	 * @ORM\Column(type="array")
	 * @var array
	 */
	protected $roles;

	/**
	 * @ORM\Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	protected $token = NULL;

	/**
	 * @ORM\PrePersist
	 */
	public function prePersist()
	{
		if ($this->createdAt === NULL) {
			$this->createdAt = new DateTime();
		}
	}

	/**
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 *
	 * @return UserEntity
	 */
	public function setUsername(string $username): UserEntity
	{
		$this->username = $username;

		return $this;
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
	 *
	 * @return UserEntity
	 */
	public function setEmail(string $email): UserEntity
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmailToChange()
	{
		return $this->emailToChange;
	}

	/**
	 * @param string $emailToChange
	 *
	 * @return UserEntity
	 */
	public function setEmailToChange(string $emailToChange = NULL): UserEntity
	{
		$this->emailToChange = $emailToChange;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 *
	 * @return UserEntity
	 */
	public function setPassword(string $password = NULL): UserEntity
	{
		$this->password = $password;

		return $this;
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
	 *
	 * @return UserEntity
	 */
	public function setCreatedAt(DateTime $createdAt): UserEntity
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}

	/**
	 * @param DateTime $updatedAt
	 *
	 * @return UserEntity
	 */
	public function setUpdatedAt(DateTime $updatedAt = NULL): UserEntity
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getRoles(): array
	{
		return $this->roles;
	}

	/**
	 * @param array $roles
	 *
	 * @return UserEntity
	 */
	public function setRoles(array $roles): UserEntity
	{
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * @param string $token
	 *
	 * @return UserEntity
	 */
	public function setToken(string $token = NULL): UserEntity
	{
		$this->token = $token;

		return $this;
	}
}