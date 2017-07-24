<?php

namespace Shortener\Security;

use Nette\Object;
use Nette\Security\Passwords;

/**
 * Hash passwords
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Security
 */
class PasswordHash extends Object
{
	/** @var string */
	private $salt;

	/**
	 * PasswordHash constructor.
	 *
	 * @param string $salt
	 */
	public function __construct($salt)
	{
		$this->salt = $salt;
	}

	/**
	 * Hashes password
	 *
	 * @param string $password
	 *
	 * @return string
	 */
	public function hashPassword($password)
	{
		return Passwords::hash($password, ['salt' => $this->salt]);
	}
}