<?php

namespace Shortener\Facade\Link\Doctrine;

use Shortener\Application\Doctrine\Facade\ICRUDFacade;
use Shortener\Domain\Link\LinkEntity;

/**
 * Interface for link facade
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Facade\Link
 */
interface ILinkFacade extends ICRUDFacade
{
	/**
	 * @param string $url
	 *
	 * @return LinkEntity|NULL
	 */
	public function findByUrl(string $url);

	/**
	 * @param string $hash
	 *
	 * @return LinkEntity|NULL
	 */
	public function findByHash(string $hash);

	/**
	 * Find by url and user id
	 *
	 * @param string   $url
	 *
	 * @param int|NULL $userId
	 *
	 * @return LinkEntity|NULL
	 */
	public function findByUrlUser(string $url, int $userId = NULL);
}