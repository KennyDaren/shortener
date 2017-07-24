<?php

namespace Shortener\Security;

use Nette\Security\Permission;

/**
 * Access Control List
 *
 * @author Hynek Nerad <iam@kennydaren.me>
 */
class ACL extends Permission
{
	const ACTION_CREATE = 'create';
	const ACTION_EDIT = 'edit';
	const ACTION_DELETE = 'delete';
	const ACTION_LIST = 'list';
	const ACTION_VIEW = 'view';

	const RESOURCE_DASHBOARD = 'dashboard';
	const RESOURCE_USERS = 'users';
	const RESOURCE_ADMINS = 'admins';

	private $allActions = [
		self::ACTION_CREATE,
		self::ACTION_EDIT,
		self::ACTION_DELETE,
		self::ACTION_LIST,
		self::ACTION_VIEW,
	];

	private $allResources = [
		self::RESOURCE_DASHBOARD,
		self::RESOURCE_USERS,
		self::RESOURCE_ADMINS
	];

	/**
	 * AclDefinitions constructor
	 */
	public function __construct()
	{
		$this->addRole(Roles::CLIENT);
		$this->addRole(Roles::ADMIN);
		$this->addRole('guest');

		foreach ($this->allResources as $resource) {
			$this->addResource($resource);
		}

		// admins allows all
		$this->allow(Roles::ADMIN, $this->allResources, $this->allActions);
		$this->deny(Roles::CLIENT, $this->allResources, $this->allActions);
		$this->deny('guest', $this->allResources, $this->allActions);
	}
}
