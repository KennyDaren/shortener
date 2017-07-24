<?php

namespace Shortener\RabbitMQ\Consumer;

use BrowscapPHP\Browscap;
use DateTime;
use GeoIp2\Database\Reader;
use Kdyby\RabbitMq\IConsumer;
use PhpAmqpLib\Message\AMQPMessage;
use Shortener\Configuration\Configuration;
use Shortener\Domain\Stats\StatsEntity;
use Shortener\Facade\Link\Doctrine\ILinkFacade;
use Shortener\Facade\Stats\IStatsFacade;
use Shortener\RabbitMQ\Message\LinkStatMessage;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Device;
use Sinergi\BrowserDetector\UserAgent;

/**
 * Consumer for saving link stats and fetching additional data
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\RabbitMQ\Consumer
 */
class LinkStatConsumer implements IConsumer
{
	/** @var  ILinkFacade */
	private $linkFacade;
	/** @var IStatsFacade */
	private $statsFacade;
	/** @var Configuration */
	private $configuration;
	/** @var Reader */
	private $reader;
	/** @var  Browscap */
	private $browscap;

	public function __construct(IStatsFacade $statsFacade,
	                            ILinkFacade $linkFacade,
	                            Configuration $configuration,
	                            Reader $reader,
	                            Browscap $browscap)
	{
		$this->statsFacade = $statsFacade;
		$this->configuration = $configuration;
		$this->reader = $reader;
		$this->linkFacade = $linkFacade;
		$this->browscap = $browscap;
	}

	//better way is override consumer implementation to create and message object
	//but there are only 2 consumers
	/**
	 * @param AMQPMessage $message
	 *
	 * @return int
	 */
	public function process(AMQPMessage $message): int
	{
		$statMessage = new LinkStatMessage();
		$statMessage->unserialize($message->getBody());

		$browser = $this->browscap->getBrowser($statMessage->userAgent);

		if ($browser->crawler) { //skip crawlers
			return IConsumer::MSG_ACK;
		}

		$linkEntity = $this->linkFacade->findByHash($statMessage->hash);

		if ($linkEntity === NULL) {
			return IConsumer::MSG_REJECT;
		}

		//for running on localhost
		$devLocalIps = $this->configuration->getDevLocalIps();
		if (!empty($devLocalIps)) {
			$statMessage->ip = $devLocalIps[rand(0, count($devLocalIps) - 1)];
		}

		$geoRecord = $this->reader->city($statMessage->ip);

		$values = [
			'link'      => $linkEntity->getId(),
			'user'      => $linkEntity->getUser() !== NULL ? $linkEntity->getUser()->getId() : NULL,
			'dateTime'  => DateTime::createFromFormat(DATE_ISO8601, $statMessage->dateTime),
			'city'      => $geoRecord->city->name,
			'country'   => $geoRecord->country->name,
			'latitude'  => $geoRecord->location->latitude,
			'longitude' => $geoRecord->location->longitude,
			'browser'   => $browser->browser,
			'device'    => $browser->istablet ? StatsEntity::DEVICE_TABLET :
				$browser->ismobiledevice ? StatsEntity::DEVICE_MOBILE : StatsEntity::DEVICE_PC,
			'platform'  => $browser->platform,
			'referer'   => $statMessage->referer
		];

		$this->statsFacade->save($values);

		return IConsumer::MSG_ACK;
	}
}