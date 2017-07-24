<?php

namespace Shortener\RabbitMQ\Message;

use DateTime;
use Shortener\Application\RabbitMQ\Message;

/**
 * Message for Link stat consumer
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\RabbitMQ\Message
 */
class LinkStatMessage extends Message
{
	/** @var string */
	public $hash;

	/** @var DateTime */
	public $dateTime;

	/** @var string */
	public $ip;

	/** @var string */
	public $userAgent;

	/** @var string */
	public $referer;
}