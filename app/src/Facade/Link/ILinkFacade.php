<?php

namespace Shortener\Facade\Link;

use Nette\Http\IRequest;
use Nette\Http\Request;
use Shortener\Application\Facade\IFacade;
use Shortener\Domain\Link\LinkEntity;
use Shortener\Exception\RecordNotFoundException;

/**
 * Interface for link (logic) facade
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Facade\Link
 */
interface ILinkFacade extends IFacade
{
	/**
	 * @param string   $url
	 * @param int|NULL $userId
	 *
	 * @return LinkEntity
	 */
	public function create(string $url, int $userId = NULL): LinkEntity;

	/**
	 * Get redirect url and send stats about it
	 *
	 * @param string   $hash
	 * @param IRequest $request
	 *
	 * @return string
	 * @throws RecordNotFoundException
	 */
	public function getRedirect(string $hash, IRequest $request): string;
}