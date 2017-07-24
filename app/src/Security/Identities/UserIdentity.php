<?php

namespace Shortener\Security\Identity;

use Shortener\Domain\User\UserEntity;
use Nette\Object;
use Nette\Security\IIdentity;

/**
 * User identity
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Security\Identity
 */
class UserIdentity extends Object implements IIdentity
{
	/** @var string */
	private $username;

	/** @var string */
	private $email;

	/** @var int */
	private $id;

	/** @var array */
	private $roles;

	/**
	 * BaseUserIdentity constructor.
	 *
	 * @param UserEntity $userEntity
	 */
	public function __construct(UserEntity $userEntity)
	{
		$this->username = $userEntity->getUsername();
		$this->id = $userEntity->getId();
		$this->roles = $userEntity->getRoles();
		$this->email = $userEntity->getEmail();
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
	 * @return UserIdentity
	 */
	public function setUsername(string $username): UserIdentity
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
	 * @return UserIdentity
	 */
	public function setEmail(string $email): UserIdentity
	{
		$this->email = $email;

		return $this;
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
	 *
	 * @return UserIdentity
	 */
	public function setId(int $id): UserIdentity
	{
		$this->id = $id;

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
	 * @return UserIdentity
	 */
	public function setRoles(array $roles): UserIdentity
	{
		$this->roles = $roles;

		return $this;
	}
}