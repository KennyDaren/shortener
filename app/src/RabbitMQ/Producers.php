<?php

namespace Shortener\RabbitMQ;

use Kdyby\RabbitMq\Producer;

/**
 * Producers holder
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\RabbitMQ
 */
class Producers
{
	/** @var Producer */
	private $mailSend;
	/** @var Producer */
	private $linkStat;

	public function __construct(Producer $mailSend,
	                            Producer $linkStat)
	{
		$this->mailSend = $mailSend;
		$this->linkStat = $linkStat;
	}

	/**
	 * @return Producer
	 */
	public function getMailSend(): Producer
	{
		return $this->mailSend;
	}

	/**
	 * @return Producer
	 */
	public function getLinkStat(): Producer
	{
		return $this->linkStat;
	}
}