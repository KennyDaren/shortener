<?php

namespace Shortener\Security;

/**
 * Security ACL Roles
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Security
 */
class Roles
{
	const ADMIN = 'admin';
	const CLIENT = 'client';

	/**
	 * Only static call
	 */
	private function __construct()
	{
	}

	/**
	 * Get all roles
	 *
	 * @return array
	 */
	public static function getAll()
	{
		return [self::ADMIN, self::CLIENT];
	}
}