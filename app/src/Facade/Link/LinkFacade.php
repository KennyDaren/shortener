<?php

namespace Shortener\Facade\Link;

use DateTime;
use Nette\Caching\Cache;
use Nette\Http\IRequest;
use Nette\Http\Request;
use Shortener\Configuration\Configuration;
use Shortener\Domain\Link\LinkEntity;
use Shortener\Exception\InvalidStateException;
use Shortener\Exception\RecordNotFoundException;
use Shortener\Facade\Link\Doctrine\ILinkFacade as ILinkDoctrineFacade;
use Shortener\RabbitMQ\Message\LinkStatMessage;
use Shortener\RabbitMQ\Producers;

/**
 * Link (logic) facade
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Facade\Link
 */
class LinkFacade implements ILinkFacade
{

	/** @var ILinkDoctrineFacade */
	private $linkFacade;
	/** @var Cache */
	private $cache;
	/** @var Configuration */
	private $configuration;
	/** @var Producers */
	private $producers;

	/**
	 * LinkFacade constructor.
	 *
	 * @param ILinkDoctrineFacade $linkFacade
	 * @param Cache               $cache
	 * @param Configuration       $configuration
	 * @param Producers           $producers
	 */
	public function __construct(ILinkDoctrineFacade $linkFacade,
	                            Cache $cache,
	                            Configuration $configuration,
	                            Producers $producers)
	{
		$this->linkFacade = $linkFacade;
		$this->cache = $cache;
		$this->configuration = $configuration;
		$this->producers = $producers;
	}

	/**
	 * Creates link with hash (or return already existing if is anonymous)
	 *
	 * @param string   $url
	 * @param int|NULL $userId
	 *
	 * @return LinkEntity
	 */
	public function create(string $url, int $userId = NULL): LinkEntity
	{
		$link = NULL;
		if ($userId === NULL) { // find already existing if anonymous
			$link = $this->linkFacade->findByUrlUser($url, NULL);
		}

		if ($link !== NULL && $link->getUser() === NULL) {
			return $link;
		}

		/** @var LinkEntity $link */
		$link = $this->linkFacade->save([
			'url'  => $url,
			'user' => $userId
		]);

		return $this->linkFacade->save([
			'id'     => $link->getId(),
			'hash'   => $this->idToHash($link->getId()),
			'status' => LinkEntity::STATUS_ACTIVE
		]);
	}

	/**
	 * Get redirect url and send stats about it
	 *
	 * @param string   $hash
	 * @param IRequest $request
	 *
	 * @return string
	 * @throws RecordNotFoundException
	 */
	public function getRedirect(string $hash, IRequest $request): string
	{
		//try find in cache
		$url = $this->cache->load($hash);

		if ($url === NULL) {
			$link = $this->linkFacade->findByHash($hash); //try find in db

			if ($link === NULL || $link->getStatus() !== LinkEntity::STATUS_ACTIVE) {
				throw new RecordNotFoundException(sprintf('Link with hash "%s" does not exists', $hash));
			}

			$url = $this->cache->save($hash, $link->getUrl(), [
				Cache::EXPIRE  => $this->configuration->getLinkCacheExpiration(),
				Cache::SLIDING => TRUE
			]);
		}

		//send data to link stat consumer
		$message = new LinkStatMessage();
		$message->hash = $hash;
		$message->dateTime = new DateTime();
		$message->ip = $request->getRemoteAddress();
		$message->userAgent = $request->getHeader('User-Agent');

		if ($request instanceof Request) {
			$message->referer = (string)$request->getReferer();
		}

		$this->producers->getLinkStat()->publish($message->serialize());

		return $url;
	}

	/**
	 * Converts id to hash
	 *
	 * @param int $id
	 *
	 * @return string
	 * @throws InvalidStateException
	 */
	private function idToHash(int $id): string
	{
		if ($id < 1) {
			throw new InvalidStateException(
				'The ID cannot be negative integer value');
		}

		$chars = $this->configuration->getUrlHashCharacters();
		$length = strlen($chars);

		$code = '';
		while ($id > $length - 1) {
			$code = $chars[fmod($id, $length)] . $code;
			$id = (int)floor($id / $length);
		}

		return $chars[$id] . $code;
	}
}