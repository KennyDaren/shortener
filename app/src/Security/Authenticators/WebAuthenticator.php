<?php

namespace Shortener\Security\Authenticators;

use Kdyby\Translation\Translator;
use Shortener\Domain\User\UserEntity;
use Shortener\Facade\Users\IUserFacade;
use Shortener\Security\Identity\UserIdentity;
use Shortener\Security\PasswordHash;
use Nette\Security\AuthenticationException;
use Nette\Security\User;

/**
 * Admin authenticator
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Security\Authenticators
 */
class WebAuthenticator
{
	/** @var User */
	private $user;

	/** @var PasswordHash */
	private $passwordHash;

	/** @var IUserFacade */
	private $baseUserFacade;

	/** @var Translator */
	private $translator;

	/**
	 * ClientAuthenticator constructor.
	 *
	 * @param User         $user
	 * @param PasswordHash $passwordHash
	 * @param IUserFacade  $baseUserFacade
	 * @param Translator   $translator
	 */
	public function __construct(User $user,
	                            PasswordHash $passwordHash,
	                            IUserFacade $baseUserFacade,
	                            Translator $translator)
	{
		$this->user = $user;
		$this->passwordHash = $passwordHash;
		$this->baseUserFacade = $baseUserFacade;
		$this->translator = $translator;
	}

	/**
	 * Login
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @throws AuthenticationException
	 */
	public function login($username, $password)
	{
		$user = $this->baseUserFacade->findByUsername($username);

		if ($user === NULL || $user->getStatus() !== UserEntity::STATUS_ACTIVE) {
			throw new AuthenticationException('Authorization failed');
		}

		if ($this->passwordHash->hashPassword($password) !== $user->getPassword()) {
			throw new AuthenticationException('Authorization failed');
		}

		$this->user->login(new UserIdentity($user));
	}
}