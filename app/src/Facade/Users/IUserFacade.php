<?php

namespace Shortener\Facade\Users;

use Nette\Utils\Random;
use Nette\Utils\Strings;
use Shortener\Application\Doctrine\Facade\ICRUDFacade;
use Shortener\Domain\User\UserEntity;
use Shortener\Exception\EmailExistsException;
use Shortener\Exception\InvalidStateException;
use Shortener\Exception\RecordNotFoundException;
use Shortener\Exception\UsernameExistsException;
use Shortener\Mail\IMailSendService;

/**
 * I base user facade
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Facade\Users
 */
interface IUserFacade extends ICRUDFacade
{


	/**
	 * Find user by email
	 *
	 * @param string $email
	 *
	 * @return UserEntity|NULL
	 */
	public function findByEmail(string $email);

	/**
	 * Find user by username
	 *
	 * @param string $username
	 *
	 * @return UserEntity|NULL
	 *
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByUsername(string $username);

	/**
	 * Find user by generated token
	 *
	 * @param string $token
	 *
	 * @return UserEntity|NULL
	 */
	public function findUserByToken(string $token);


	/**
	 * @param array $values
	 *
	 * @return UserEntity
	 * @throws EmailExistsException
	 * @throws UsernameExistsException
	 */
	public function create(array $values);
}